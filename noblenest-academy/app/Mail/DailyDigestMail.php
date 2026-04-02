<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $parent,
        public readonly array $digest
    ) {}

    public function envelope(): Envelope
    {
        $childName = data_get($this->digest, 'summaries.0.child_name', 'your child');

        return new Envelope(
            subject: "✨ {$childName}'s Learning Summary – " . now()->subDay()->format('M j'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-digest',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
