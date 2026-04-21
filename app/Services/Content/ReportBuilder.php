<?php

namespace App\Services\Content;

class ReportBuilder
{
    public function build($stats, $lang = 'ar', $schedule = null, $narrative = null)
    {
        return $lang === 'en'
            ? $this->buildEnglish($stats)
            : $this->buildArabic($stats, $schedule, $narrative);
    }

    // =========================
    // 🇸🇦 Arabic Version
    // =========================
    private function buildArabic($stats, $schedule = null, $narrative = null)
    {
        if ($schedule === 'daily') {
            return $this->dailyReportAr($stats);
        }

        if ($schedule === 'weekly') {
            return $this->weeklyReportAr($stats);
        }

        if ($schedule === 'monthly') {
            return $this->monthlyReportAr($stats);
        }
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'عدة مناطق';
        $count = $stats['top_governorate']->count ?? 0;

        $hooks = [
            "تشهد الساحة الميدانية تطورات متسارعة خلال الفترة الأخيرة،",
            "في مشهد يعكس تصاعداً ملحوظاً في الأحداث،",
            "تستمر وتيرة الأحداث بالارتفاع بشكل لافت،",
            "تعكس البيانات الأخيرة واقعاً متقلباً على الأرض،"
        ];

        $analysis = [
            "ما يشير إلى تغير في نمط التوزع الجغرافي للأحداث.",
            "في مؤشر على تصاعد التوتر في بعض المناطق.",
            "وهو ما يعكس حالة من عدم الاستقرار المتزايد.",
            "ما يطرح تساؤلات حول اتجاهات التصعيد القادمة."
        ];

        $trendText = '';

        if ($narrative) {
            if ($narrative['trend'] === 'sharp_increase') {
                $trendText = 'تشير البيانات إلى تصاعد حاد في وتيرة الأحداث، ';
            } elseif ($narrative['trend'] === 'increase') {
                $trendText = 'تظهر البيانات ارتفاعاً ملحوظاً في الأحداث، ';
            } elseif ($narrative['trend'] === 'decrease') {
                $trendText = 'تشير البيانات إلى تراجع في وتيرة الأحداث، ';
            } else {
                $trendText = 'تعكس البيانات حالة مستقرة نسبياً، ';
            }
        }
        $hook = $this->pick($hooks);
        $analysisText = $this->pick($analysis);

        $summary = "{$trendText}{$hook} حيث تم تسجيل {$total} حادثة موثقة، "
            . "تصدرت {$gov} المشهد بـ {$count} حالة، {$analysisText}";

        return [
            'type' => 'report',
            'priority' => 'medium',
            'data' => [
                'title' => "📊 تقرير ميداني",
                'summary' => $summary,
                'footer' => "⚠️ تستند هذه البيانات إلى الأحداث الموثقة ضمن النظام"
            ]
        ];
    }

    // =========================
    // 🇬🇧 English Version
    // =========================
    private function buildEnglish($stats)
    {
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'multiple areas';
        $count = $stats['top_governorate']->count ?? 0;

        $hooks = [
            "Recent data indicates a notable escalation in events,",
            "The current situation reflects increasing instability,",
            "Field reports show a rising trend in incidents,",
            "The latest data highlights a shifting pattern on the ground,"
        ];

        $analysis = [
            "suggesting a shift in geographical distribution.",
            "indicating rising tensions in key areas.",
            "highlighting growing instability.",
            "raising concerns about further escalation."
        ];

        $hook = $this->pick($hooks);
        $analysisText = $this->pick($analysis);

        $summary = "{$hook} with {$total} recorded incidents, "
            . "{$gov} leading with {$count} cases, {$analysisText}";

        return [
            'type' => 'report',
            'priority' => 'medium',
            'data' => [
                'title' => "📊 Field Report",
                'summary' => $summary,
                'footer' => "⚠️ Data is based on verified incidents"
            ]
        ];
    }

    private function pick($arr)
    {
        return $arr[array_rand($arr)];
    }

    private function dailyReportAr($stats)
    {
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'عدة مناطق';

        return [
            'type' => 'report',
            'priority' => 'medium',
            'data' => [
                'title' => "📊 التقرير اليومي",
                'summary' => "تم تسجيل {$total} حادثة خلال اليوم، تركزت في {$gov}.",
                'footer' => "⚠️ بيانات يومية أولية"
            ]
        ];
    }

    private function weeklyReportAr($stats)
    {
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'عدة مناطق';

        return [
            'type' => 'report',
            'priority' => 'medium',
            'data' => [
                'title' => "📊 التقرير الأسبوعي",
                'summary' => "شهد هذا الأسبوع تسجيل {$total} حادثة، مع تصدر {$gov}.",
                'footer' => "⚠️ تحليل أسبوعي"
            ]
        ];
    }

    private function monthlyReportAr($stats)
    {
        $total = $stats['total'] ?? 0;
        $gov = $stats['top_governorate']->governorate ?? 'عدة مناطق';

        return [
            'type' => 'report',
            'priority' => 'high',
            'data' => [
                'title' => "📊 التقرير الشهري",
                'summary' => "خلال الشهر الماضي، تم تسجيل {$total} حادثة، مع تركّز واضح في {$gov}.",
                'footer' => "⚠️ قراءة شهرية"
            ]
        ];
    }
}