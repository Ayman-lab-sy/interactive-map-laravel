<?php

namespace App\Services;

use App\Services\Content\ReportBuilder;
use App\Services\Content\AlertBuilder;
use Illuminate\Http\Request;
use App\Services\Content\InsightBuilder;
use App\Services\Content\SocialBuilder;

class ContentStrategyService
{
    public function decide($stats, Request $request, $narrative = null)
    {
        $schedule = $request->get('schedule');
        if ($schedule) {
            switch ($schedule) {
                case 'daily':
                    $request->merge(['range' => 'today']);
                    break;
                case 'yesterday':
                    $request->merge(['days' => 1]);
                    break;
                case 'weekly':
                    $request->merge(['range' => 'week']);
                    break;
                case 'monthly':
                    $request->merge(['range' => 'month']);
                    break;
            }
            // 🔥 إعادة حساب الإحصائيات بعد التعديل
            $stats = app(\App\Services\StatsService::class)->getStats($request);
            // 🎯 فرض نوع التقرير
            $request->merge(['type' => 'report']);
        }

        $type = $request->get('type');
        $lang = $request->get('lang', 'ar');
        // 🎯 Manual override (لازم يكون أول شي)
        if ($type === 'alert') {
            return app(AlertBuilder::class)->build($stats, $lang);
        }

        if ($type === 'report') {
            $schedule = $request->get('schedule');
            $lang = $request->get('lang', 'ar');
            return app(ReportBuilder::class)->build($stats, $lang, $schedule, $narrative);
        }

        if ($type === 'insight') {
            return app(InsightBuilder::class)->build($stats, $lang);
        }

        if ($type === 'social') {
            return app(SocialBuilder::class)->build($stats, $lang);
        }

        // 🔥 Smart Scoring System

        $scores = [
            'alert' => 0,
            'insight' => 0,
            'report' => 0,
            'social' => 10, // default baseline
        ];

        $total = $stats['total'] ?? 0;
        $previous = $stats['previous'];

        // 🚨 Alert scoring
        if ($previous !== null && $previous > 0) {
            $percent = (($total - $previous) / $previous) * 100;

            if ($percent > 50) $scores['alert'] += 80;
            elseif ($percent > 20) $scores['alert'] += 50;
            elseif ($percent > 10) $scores['alert'] += 20;
        }

        // 📈 Insight scoring
        if (!empty($stats['timeline'])) {
            $maxDay = collect($stats['timeline'])->sortByDesc('count')->first();

            if ($maxDay) {
                if ($maxDay->count > 15) $scores['insight'] += 70;
                elseif ($maxDay->count > 10) $scores['insight'] += 50;
            }
        }

        // 📊 Report scoring
        if ($total > 50) $scores['report'] += 70;
        elseif ($total > 20) $scores['report'] += 40;

        // 🔥 اختيار الأعلى
        $topType = array_keys($scores, max($scores))[0];

        return match ($topType) {
            'alert' => app(AlertBuilder::class)->build($stats),
            'insight' => app(InsightBuilder::class)->build($stats),
            'report' => app(ReportBuilder::class)->build($stats, 'ar', null, $narrative),
            'social' => app(SocialBuilder::class)->build($stats),
        };
    }
}