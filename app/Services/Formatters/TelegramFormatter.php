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
            $this->cleanSummary($data['summary']) . "\n\n" .

            "━━━━━━━━━━━━━━\n" .
            "{$data['footer']}\n\n" .

            "📍 تابع الأحداث على الخريطة التفاعلية:\n" .
            "https://www.thealawites.com/ar/map\n\n" .

            "#سوريا #تقارير #أحداث",

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

    private function cleanSummary($text)
    {
        // حذف أي عنوان مكرر (مثل 📊 تقرير)
        $text = preg_replace('/^📊.*\n/', '', $text);

        // إزالة \n الزائدة
        $text = trim($text);

        // ترتيب الأسطر
        $lines = explode("\n", $text);
        $lines = array_filter(array_map('trim', $lines));

        return implode("\n\n", $lines);
    }
}