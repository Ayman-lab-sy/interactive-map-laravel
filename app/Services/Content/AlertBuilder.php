<?php

namespace App\Services\Content;

class AlertBuilder
{
    public function build($stats, $lang = 'ar')
    {
        return $lang === 'en'
            ? $this->buildEnglish($stats)
            : $this->buildArabic($stats);
    }

    // =========================
    // 🇸🇦 عربي (Breaking)
    // =========================
    private function buildArabic($stats)
    {
        $gov = $stats['top_governorate']->governorate ?? 'عدة مناطق';

        $diff = $stats['total'] - $stats['previous'];
        $percent = $stats['previous'] > 0
            ? round(($diff / $stats['previous']) * 100)
            : 0;

        if ($percent > 70) {
            $templates = [
                "🚨 عاجل: تصاعد خطير جداً في الأحداث داخل {$gov} (+{$percent}%)",
                "⚠️ تحذير: قفزة حادة وغير مسبوقة في الأحداث في {$gov}",
            ];
        } elseif ($percent > 40) {
            $templates = [
                "🚨 عاجل: ارتفاع كبير في وتيرة الأحداث داخل {$gov}",
                "⚠️ تصاعد واضح في الأحداث في {$gov} خلال الفترة الأخيرة",
            ];
        } else {
            $templates = [
                "📢 تنبيه: ارتفاع ملحوظ في الأحداث داخل {$gov}",
                "⚠️ تزايد تدريجي في الأحداث في {$gov}",
            ];
        }

        return [
            'type' => 'alert',
            'priority' => 'high',
            'data' => [
                'text' => $this->pick($templates)
            ]
        ];
    }

    // =========================
    // 🇬🇧 English (Breaking)
    // =========================
    private function buildEnglish($stats)
    {
        $gov = $stats['top_governorate']->governorate ?? 'multiple areas';

        $diff = $stats['total'] - $stats['previous'];
        $percent = $stats['previous'] > 0
            ? round(($diff / $stats['previous']) * 100)
            : 0;

        if ($percent > 70) {
            $templates = [
                "🚨 Breaking: Sharp and critical escalation in {$gov} (+{$percent}%)",
                "⚠️ Alert: Unusual spike in events in {$gov}",
            ];
        } elseif ($percent > 40) {
            $templates = [
                "🚨 Breaking: Significant rise in events in {$gov}",
                "⚠️ Noticeable escalation detected in {$gov}",
            ];
        } else {
            $templates = [
                "📢 Alert: Increasing activity in {$gov}",
                "⚠️ Gradual rise observed in {$gov}",
            ];
        }

        return [
            'type' => 'alert',
            'priority' => 'high',
            'data' => [
                'text' => $this->pick($templates)
            ]
        ];
    }

    private function pick($arr)
    {
        return $arr[array_rand($arr)];
    }
}