<?php

namespace App\Jobs;

use App\Models\AuditLogEntry;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\ConsentReceipt;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Phase 5 — GDPR Article 20 data-portability export.
 *
 * Gathers parent + children + progress + consent receipts + payments into a
 * zip (JSON + CSV), stores at local://private/exports/{user_id}/{ts}.zip,
 * and writes an audit log entry. Email-delivery is logged via audit; in
 * MVP we skip the actual Mail::send (no SMTP creds in test envs).
 */
class ExportParentDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId)
    {
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            return;
        }

        $ts = now()->format('YmdHis');
        $children = ChildProfile::withTrashed()
            ->where('parent_id', $user->id)
            ->get();

        $childIds = $children->pluck('id')->all();

        $payload = [
            'account' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => optional($user->created_at)->toIso8601String(),
            ],
            'children' => $children->map(fn (ChildProfile $c) => [
                'id'                  => $c->id,
                'name'                => $c->name,
                'date_of_birth'       => optional($c->date_of_birth)->toDateString(),
                'preferred_language'  => $c->preferred_language,
                'parental_consent_at' => optional($c->parental_consent_at)->toIso8601String(),
            ])->all(),
            'progress' => ChildActivityProgress::withTrashed()
                ->whereIn('child_profile_id', $childIds)
                ->get(['id', 'child_profile_id', 'activity_id', 'status', 'score', 'completed_at'])
                ->toArray(),
            'consent_receipts' => ConsentReceipt::where('parent_user_id', $user->id)
                ->get()->toArray(),
            'payments' => Payment::where('user_id', $user->id)
                ->get(['id', 'amount', 'currency', 'status', 'created_at'])
                ->toArray(),
            'exported_at' => now()->toIso8601String(),
        ];

        $disk = Storage::disk('local');
        $dir  = "private/exports/{$user->id}";
        $disk->makeDirectory($dir);

        $jsonPath = "{$dir}/{$ts}.json";
        $disk->put($jsonPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Best-effort zip + CSV. ZipArchive is widely available; if missing,
        // fall back to the JSON file alone.
        $zipPath = "{$dir}/{$ts}.zip";
        $zipFull = $disk->path($zipPath);
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipFull, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $zip->addFromString('data.json', (string) $disk->get($jsonPath));
                $zip->addFromString('children.csv', $this->toCsv($payload['children']));
                $zip->addFromString('progress.csv', $this->toCsv($payload['progress']));
                $zip->addFromString('payments.csv', $this->toCsv($payload['payments']));
                $zip->close();
            }
        }

        $signed = URL::temporarySignedRoute(
            'privacy.export.download',
            now()->addHour(),
            ['user' => $user->id, 'ts' => $ts],
        );

        AuditLogEntry::record(
            actorUserId: $user->id,
            action: 'privacy.export.generated',
            targetType: User::class,
            targetId: $user->id,
            meta: ['ts' => $ts, 'signed_url' => $signed],
        );

        // Email delivery is best-effort — skip silently if mail not configured.
        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Your Noble Nest Academy data export is ready. Download (link expires in 1 hour):\n\n{$signed}",
                function ($m) use ($user) {
                    $m->to($user->email)->subject('Your Noble Nest Academy data export');
                }
            );
        } catch (\Throwable $e) {
            // Logged via audit; do not fail the job.
            \Illuminate\Support\Facades\Log::warning('ExportParentDataJob mail send failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function toCsv(array $rows): string
    {
        if (empty($rows)) {
            return "";
        }
        $headers = array_keys((array) $rows[0]);
        $out = fopen('php://temp', 'r+');
        if ($out === false) {
            return "";
        }
        fputcsv($out, $headers);
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $h) {
                $v = $row[$h] ?? '';
                $line[] = is_scalar($v) ? (string) $v : json_encode($v);
            }
            fputcsv($out, $line);
        }
        rewind($out);
        $csv = stream_get_contents($out) ?: '';
        fclose($out);
        return $csv;
    }
}
