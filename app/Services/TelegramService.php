<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function sendMessage($text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        return Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }

    public function sendPhoto($imagePath, $caption = '')
    {
        $token = config('services.telegram.token');
        $chatId = config('services.telegram.chat_id');

        $response = \Http::attach(
            'photo',
            file_get_contents($imagePath),
            'report.png'
        )->post("https://api.telegram.org/bot{$token}/sendPhoto", [
            'chat_id' => $chatId,
            'caption' => $caption,
        ]);

        $data = $response->json();

        // ✅ تحقق احترافي (مو dd)
        if (!$response->successful() || !($data['ok'] ?? false)) {
            \Log::error("TELEGRAM FAILED", [
                'response' => $data
            ]);
            return false;
        }

        \Log::info("TELEGRAM SENT", [
            'chat_id' => $chatId
        ]);

        return true;
    }
}