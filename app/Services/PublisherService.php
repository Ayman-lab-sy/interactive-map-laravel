<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Services\Formatters\TelegramFormatter;
use App\Services\TelegramService;

class PublisherService
{
    public function publish($imagePath, $post, $channels = ['telegram'])
    {
        foreach ($channels as $channel) {
            try {
                match ($channel) {
                    'telegram' => $this->publishToTelegram($imagePath, $post),
                    // 🔜 مستقبلاً
                    'facebook' => $this->publishToFacebook($imagePath, $post),
                    'instagram' => $this->publishToInstagram($imagePath, $post),
                    'tiktok' => $this->publishToTikTok($imagePath, $post),

                    default => null,
                };
                // ✅ نجاح
                Log::info('PUBLISH SUCCESS', [
                    'channel' => $channel,
                    'type' => $post['type'] ?? null,
                    'time' => now()
                ]);
            } catch (\Exception $e) {
                // ❌ فشل
                Log::error('PUBLISH FAILED', [
                    'channel' => $channel,
                    'error' => $e->getMessage(),
                    'time' => now()
                ]);
            }
        }
    }

    private function publishToTelegram($imagePath, $post)
    {
        $formatted = app(\App\Services\Formatters\TelegramFormatter::class)
            ->format($post);
        $maxAttempts = 3;
        for ($i = 1; $i <= $maxAttempts; $i++) {
            try {
                $success = app(\App\Services\TelegramService::class)
                    ->sendPhoto($imagePath, $formatted);
                if ($success) {
                    \Log::info("TELEGRAM SUCCESS", [
                        'attempt' => $i
                    ]);
                    return true;
                }
            } catch (\Exception $e) {
                \Log::error("TELEGRAM ERROR", [
                    'attempt' => $i,
                    'error' => $e->getMessage()
                ]);
            }
            // ⏳ انتظار قبل المحاولة التالية
            sleep(2);
        }
        // ❌ فشل نهائي
        \Log::critical("TELEGRAM FAILED AFTER RETRIES");
        return false;
    }

    private function publishToFacebook($imagePath, $post)
    {
        // TODO: implement
    }

    private function publishToInstagram($imagePath, $post)
    {
        // TODO: implement
    }

    private function publishToTikTok($imagePath, $post)
    {
        // TODO: implement
    }
}