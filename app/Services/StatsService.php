<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Http\Request;

class StatsService
{
    public function getStats(Request $request)
    {
        $query = Event::where('status', 'verified');

        // 🎯 فلتر المحافظة
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // 🎯 فلتر النوع
        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        // 🎯 فلتر الزمن
        if ($request->filled('days')) {
            $query->where('event_date', '>=', now()->subDays($request->days));
        } elseif ($request->filled('range')) {

            if ($request->range === 'today') {
                $query->whereBetween('event_date', [
                    now()->startOfDay(),
                    now()->endOfDay()
                ]);
            }

            if ($request->range === 'week') {
                $query->where('event_date', '>=', now()->subDays(7));
            }

            if ($request->range === 'month') {
                $query->where('event_date', '>=', now()->subDays(30));
            }
        }

        // 📊 إجمالي
        $total = (clone $query)->count();

        // 🔥 مقارنة زمنية (يدعم days + range)
        $previousPeriod = null;

        // ✅ إذا days
        if ($request->filled('days')) {
            $previousPeriod = (clone $query)
                ->whereBetween('event_date', [
                    now()->subDays($request->days * 2),
                    now()->subDays($request->days)
                ])
                ->count();
        }

        // ✅ إذا range
        elseif ($request->filled('range')) {

            if ($request->range === 'week') {
                $previousPeriod = (clone $query)
                    ->whereBetween('event_date', [
                        now()->subDays(14),
                        now()->subDays(7)
                    ])
                    ->count();
            }

            elseif ($request->range === 'month') {
                $previousPeriod = (clone $query)
                    ->whereBetween('event_date', [
                        now()->subDays(60),
                        now()->subDays(30)
                    ])
                    ->count();
            }

            elseif ($request->range === 'today') {
                $previousPeriod = (clone $query)
                    ->whereBetween('event_date', [
                        now()->subDays(1)->startOfDay(),
                        now()->subDays(1)->endOfDay()
                    ])
                    ->count();
            }
        }

        // 📊 حسب النوع
        $byCategory = (clone $query)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        // 📈 Timeline
        $timeline = (clone $query)
            ->selectRaw('DATE(event_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 🔥 المحافظات
        $topGovernorates = (clone $query)
            ->selectRaw('governorate, COUNT(*) as count')
            ->whereNotNull('governorate')
            ->groupBy('governorate')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $topGovernorate = (clone $query)
            ->selectRaw('governorate, COUNT(*) as count')
            ->whereNotNull('governorate')
            ->groupBy('governorate')
            ->orderByDesc('count')
            ->first();

        $byCategoryPercent = (clone $query)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->map(function ($item) use ($total) {
                return [
                    'category' => $item->category,
                    'count' => $item->count,
                    'percent' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0
                ];
            });

        $byGovernorate = (clone $query)
            ->selectRaw('governorate, COUNT(*) as count')
            ->whereNotNull('governorate')
            ->groupBy('governorate')
            ->pluck('count', 'governorate');

        return [
            'total' => $total,
            'previous' => $previousPeriod,
            'by_category' => $byCategory,
            'timeline' => $timeline,
            'top_governorates' => $topGovernorates,
            'top_governorate' => $topGovernorate,
            'by_governorate' => $byGovernorate,
            'by_category_percent' => $byCategoryPercent
        ];
    }
}