<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class AlertDetectionService
{
    public function detect()
    {
        // 🔥 آخر ساعة حسب event_date
        $events = Event::where('status', 'verified')
            ->where('event_date', '>=', now()->subHour())
            ->get();

        if ($events->isEmpty()) {
            return null;
        }

        // 🔥 تجميع حسب المحافظة
        $grouped = $events->groupBy('governorate');

        foreach ($grouped as $gov => $items) {

            if (!$gov) continue;

            $count = count($items);

            // 🎯 Threshold
            if ($count >= 6) {
                $cacheKey = "alert_sent_" . $gov;
                // ❌ إذا في Alert سابق
                if (Cache::has($cacheKey)) {
                    continue;
                }
                // ✅ سجّل Alert
                Cache::put($cacheKey, true, now()->addHours(2));
                return [
                    'governorate' => $gov,
                    'count' => $count
                ];
            }
        }

        return null;
    }
}