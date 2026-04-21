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
            $this->normalizeText($data['summary']) . "\n\n" .

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

    private function normalizeText($text)
    {
        // حذف عنوان التقرير إذا موجود
        $text = preg_replace('/📊.*?\n/u', '', $text);

        // تحويل كل أنواع الأسطر لـ \n
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // حذف الفراغات الزائدة
        $text = trim($text);

        // حذف التكرار في الأسطر الفارغة
        $text = preg_replace("/\n{2,}/", "\n\n", $text);

        return $text;
    }
}