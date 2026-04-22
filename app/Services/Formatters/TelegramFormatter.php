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
            $this->addSpacing($data['summary']) . "\n\n" .

            "──────────────\n" .
            "{$data['footer']}\n\n" .

            "📍 تابع الأحداث على الخريطة:\n" .
            "https://www.thealawites.com/ar/map\n\n" .

            "#سوريا #تقارير #خريطة_تفاعلية",

            'insight' => "📈 تحليل:\n\n{$data['text']}\n\n#سوريا",

            'social' => "📢 {$data['content']}\n\n" . implode(' ', $data['hashtags'] ?? []),

            default => "📢 تحديث جديد"
        };
    }

    private function addSpacing($text)
    {
        // نفصل أول سطر (العنوان) عن الباقي
        $parts = explode("\n", $text, 2);

        if (count($parts) === 2) {
            return $parts[0] . "\n\n" . $parts[1];
        }

        return $text;
    }
}