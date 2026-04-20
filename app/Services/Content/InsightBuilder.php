<?php

namespace App\Services\Content;

class InsightBuilder
{
    public function build($stats, $lang = 'ar')
    {
        return $lang === 'en'
            ? $this->buildEnglish($stats)
            : $this->buildArabic($stats);
    }

    // =========================
    // 🇸🇦 عربي (تحليل)
    // =========================
    private function buildArabic($stats)
    {
        $timeline = $stats['timeline'] ?? [];

        if (empty($timeline)) {
            return null;
        }

        $data = collect($timeline)->values();

        $first = $data->first()->count ?? 0;
        $last = $data->last()->count ?? 0;

        $maxDay = $data->sortByDesc('count')->first();

        // 🔥 تحليل الاتجاه
        if ($last > $first * 1.5) {
            $trend = "📈 يظهر اتجاه تصاعدي واضح في وتيرة الأحداث";
        } elseif ($last < $first * 0.7) {
            $trend = "📉 تراجع ملحوظ في وتيرة الأحداث";
        } else {
            $trend = "📊 استقرار نسبي في نمط الأحداث";
        }

        // 🔥 تحليل الذروة
        $peak = "📅 سجلت الأحداث ذروتها في {$maxDay->date} بعدد {$maxDay->count} حالة";

        $templates = [
            "{$trend}، حيث {$peak}.",
            "{$peak}، {$trend}.",
            "تشير البيانات إلى أن {$trend}، مع تسجيل أعلى نشاط في {$maxDay->date}.",
        ];

        return [
            'type' => 'insight',
            'priority' => 'medium',
            'data' => [
                'text' => $this->pick($templates)
            ]
        ];
    }

    // =========================
    // 🇬🇧 English (Analysis)
    // =========================
    private function buildEnglish($stats)
    {
        $timeline = $stats['timeline'] ?? [];

        if (empty($timeline)) {
            return null;
        }

        $data = collect($timeline)->values();

        $first = $data->first()->count ?? 0;
        $last = $data->last()->count ?? 0;

        $maxDay = $data->sortByDesc('count')->first();

        if ($last > $first * 1.5) {
            $trend = "📈 a clear upward trend in events is observed";
        } elseif ($last < $first * 0.7) {
            $trend = "📉 a noticeable decline in activity is detected";
        } else {
            $trend = "📊 relative stability in event patterns";
        }

        $peak = "📅 peak activity was recorded on {$maxDay->date} with {$maxDay->count} events";

        $templates = [
            "{$trend}, where {$peak}.",
            "{$peak}, alongside {$trend}.",
            "Data suggests {$trend}, with highest activity on {$maxDay->date}.",
        ];

        return [
            'type' => 'insight',
            'priority' => 'medium',
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