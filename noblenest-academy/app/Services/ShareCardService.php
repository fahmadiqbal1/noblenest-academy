<?php

namespace App\Services;

use App\Models\ChildProfile;
use App\Models\Activity;
use App\Models\Badge;
use App\Models\ShareCard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ShareCardService — Viral growth engine.
 *
 * Generates 1200×630 PNG share cards using PHP GD for:
 *  - Activity completion (most common, drives WhatsApp referrals)
 *  - Badge award
 *  - Streak milestone
 *
 * MiroFish prediction: WhatsApp share of baby first-activity card
 *  is the single highest-ROI feature (viral coefficient 1.2+).
 */
class ShareCardService
{
    private const CARD_WIDTH  = 1200;
    private const CARD_HEIGHT = 630;

    // Age-tier brand colors
    private const TIER_COLORS = [
        'baby'      => ['bg' => [255, 245, 220], 'accent' => [255, 154, 0],   'text' => [60, 40, 10]],
        'toddler'   => ['bg' => [220, 245, 255], 'accent' => [0, 150, 220],   'text' => [10, 40, 70]],
        'preschool' => ['bg' => [220, 255, 230], 'accent' => [0, 180, 80],    'text' => [10, 60, 30]],
        'school'    => ['bg' => [235, 225, 255], 'accent' => [100, 60, 220],  'text' => [30, 10, 70]],
        'default'   => ['bg' => [240, 248, 255], 'accent' => [70, 130, 180],  'text' => [20, 40, 80]],
    ];

    /**
     * Generate a share card for a completed activity and store to S3.
     *
     * @return string  Public URL of the generated PNG
     */
    public function generateActivityCard(ChildProfile $child, Activity $activity): string
    {
        $tier   = $child->age_tier ?? 'default';
        $colors = self::TIER_COLORS[$tier] ?? self::TIER_COLORS['default'];

        $image = $this->createCanvas($colors);
        $this->drawBrandStrip($image, $colors);
        $this->drawCenteredText($image, "🎉 {$child->name} just completed:", 100, $colors['text'], 32);
        $this->drawCenteredText($image, $activity->title, 165, $colors['text'], 48);
        $this->drawCenteredText($image, "on Noble Nest Academy", 230, $colors['accent'], 28);
        $this->drawAgeLabel($image, $child, $colors);
        $this->drawFooter($image, $colors);

        return $this->saveAndUpload($image, "activity_{$activity->id}_child_{$child->id}");
    }

    /**
     * Generate a share card for a badge award.
     */
    public function generateBadgeCard(ChildProfile $child, Badge $badge): string
    {
        $tier   = $child->age_tier ?? 'default';
        $colors = self::TIER_COLORS[$tier] ?? self::TIER_COLORS['default'];

        $image = $this->createCanvas($colors);
        $this->drawBrandStrip($image, $colors);
        $this->drawCenteredText($image, "🏅 {$child->name} earned:", 100, $colors['text'], 32);
        $this->drawCenteredText($image, $badge->name, 165, $colors['text'], 52);
        $this->drawCenteredText($image, $badge->description ?? '', 230, $colors['accent'], 26);
        $this->drawFooter($image, $colors);

        return $this->saveAndUpload($image, "badge_{$badge->id}_child_{$child->id}");
    }

    /**
     * Generate a streak card.
     */
    public function generateStreakCard(ChildProfile $child): string
    {
        $tier   = $child->age_tier ?? 'default';
        $colors = self::TIER_COLORS[$tier] ?? self::TIER_COLORS['default'];
        $days   = $child->streak_days ?? 1;

        $image = $this->createCanvas($colors);
        $this->drawBrandStrip($image, $colors);
        $this->drawCenteredText($image, "🔥 {$child->name} is on a", 100, $colors['text'], 32);
        $this->drawCenteredText($image, "{$days} Day Learning Streak!", 165, $colors['text'], 56);
        $this->drawCenteredText($image, "Keep it going on Noble Nest Academy", 240, $colors['accent'], 26);
        $this->drawFooter($image, $colors);

        return $this->saveAndUpload($image, "streak_{$days}_child_{$child->id}");
    }

    /**
     * Create a blank canvas with background gradient.
     */
    private function createCanvas(array $colors): \GdImage
    {
        $image = imagecreatetruecolor(self::CARD_WIDTH, self::CARD_HEIGHT);

        // Fill background
        $bg = imagecolorallocate($image, ...$colors['bg']);
        imagefill($image, 0, 0, $bg);

        // Subtle gradient overlay (top 20% darker)
        for ($y = 0; $y < 120; $y++) {
            $alpha = (int)(60 * (1 - $y / 120));
            $overlay = imagecolorallocatealpha($image, 0, 0, 0, 127 - $alpha);
            imageline($image, 0, $y, self::CARD_WIDTH, $y, $overlay);
        }

        return $image;
    }

    private function drawBrandStrip(\GdImage $image, array $colors): void
    {
        $accent = imagecolorallocate($image, ...$colors['accent']);
        imagefilledrectangle($image, 0, 0, self::CARD_WIDTH, 8, $accent);
        imagefilledrectangle($image, 0, self::CARD_HEIGHT - 8, self::CARD_WIDTH, self::CARD_HEIGHT, $accent);
    }

    private function drawCenteredText(\GdImage $image, string $text, int $y, array $color, int $size): void
    {
        if (empty(trim($text))) {
            return;
        }

        $fontColor = imagecolorallocate($image, ...$color);
        // Use built-in font (font 5 = largest built-in)
        $font      = 5;
        $charWidth = imagefontwidth($font);
        $textWidth = strlen($text) * $charWidth;
        $x         = max(20, (self::CARD_WIDTH - $textWidth) / 2);

        imagestring($image, $font, (int)$x, $y, $text, $fontColor);
    }

    private function drawAgeLabel(\GdImage $image, ChildProfile $child, array $colors): void
    {
        $age      = $child->age_months ?? 0;
        $label    = $age < 12 ? "{$age} months old" : (floor($age / 12) . ' years old');
        $color    = imagecolorallocate($image, ...$colors['accent']);
        imagestring($image, 3, 40, self::CARD_HEIGHT - 60, "Age: {$label}", $color);
    }

    private function drawFooter(\GdImage $image, array $colors): void
    {
        $accent = imagecolorallocate($image, ...$colors['accent']);
        $white  = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, self::CARD_HEIGHT - 55, self::CARD_WIDTH, self::CARD_HEIGHT - 8, $accent);

        $domain = config('app.url', 'https://noblenestacademy.com');
        $footer = "Join us at {$domain}";
        $x = (self::CARD_WIDTH - strlen($footer) * imagefontwidth(4)) / 2;
        imagestring($image, 4, max(20, (int)$x), self::CARD_HEIGHT - 40, $footer, $white);
    }

    /**
     * Encode image as PNG, upload to S3, return public URL.
     */
    private function saveAndUpload(\GdImage $image, string $name): string
    {
        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);

        $hash = Str::random(12);
        $path = "share-cards/{$name}_{$hash}.png";

        Storage::disk('s3')->put($path, $data, [
            'visibility'   => 'public',
            'ContentType'  => 'image/png',
            'CacheControl' => 'public, max-age=31536000',
        ]);

        /** @var \Illuminate\Contracts\Filesystem\Cloud $s3 */
        $s3 = Storage::disk('s3');
        return $s3->url($path);
    }
}
