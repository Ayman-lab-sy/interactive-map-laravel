<?php

namespace App\Services\Formatters;

class TelegramFormatter
{
    public function format($content)
    {
        $type = $content['type'] ?? 'social';
        $data = $content['data'] ?? [];

        return match ($type) {

            'alert' => "🚨 {$data['text']}",

            'report' => 
            "📊 تقرير الأحداث:\n\n" .
            "{$data['summary']}\n\n" .
            "{$data['footer']}\n\n" .
            "#سوريا",

            'insight' => "📈 تحليل:\n\n{$data['text']}\n\n#سوريا",

            'social' => "📢 {$data['content']}\n\n" . implode(' ', $data['hashtags'] ?? []),

            default => "📢 تحديث جديد"
        };
    }
}