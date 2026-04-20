<?php

namespace App\Services;

use App\Models\Event;

class EventService
{
    public function getFilteredEvents($request)
    {
        $query = Event::where('status', 'verified');

        // فلتر المحافظة
        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        // فلتر التاريخ
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('event_date', [
                $request->from,
                $request->to
            ]);
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

        // فلتر الخريطة (viewport)
        if ($request->has(['north', 'south', 'east', 'west'])) {
            $query->whereBetween('lat', [$request->south, $request->north])
                ->whereBetween('lng', [$request->west, $request->east]);
        }

        return $query;
    }
}