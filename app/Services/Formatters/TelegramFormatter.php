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
            "📊 تقرير ميداني\n\n" .

            trim($data['summary']) . "\n\n" .

            "──────────────\n" .
            "{$data['footer']}\n\n" .

            "📍 تابع الأحداث على الخريطة:\n" .
            "https://your-website-link.com\n\n" .

            "#سوريا #تقارير #خريطة_تفاعلية",

            'insight' => "📈 تحليل:\n\n{$data['text']}\n\n#سوريا",

            'social' => "📢 {$data['content']}\n\n" . implode(' ', $data['hashtags'] ?? []),

            default => "📢 تحديث جديد"
        };
    }

    private function formatSummary($text)
    {
        // تقسيم الأسطر
        $lines = explode("\n", $text);

        // حذف الأسطر الفارغة الزائدة
        $lines = array_filter(array_map('trim', $lines));

        // إعادة دمج مع مسافات واضحة
        return implode("\n\n", $lines);
    }

}