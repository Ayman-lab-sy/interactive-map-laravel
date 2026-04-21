<?php

namespace App\Services;

class NarrativeEngine
{
    public function analyze(array $stats): array
    {
        return [
            'trend' => $this->detectTrend($stats),
            'risk' => $this->detectRisk($stats),
            'focus' => $stats['top_governorate'] ?? null,
        ];
    }

    private function detectTrend($stats)
    {
        $growth = $stats['growth'] ?? 0;

        if ($growth > 50) return 'sharp_increase';
        if ($growth > 20) return 'increase';
        if ($growth < -20) return 'decrease';

        return 'stable';
    }

    private function detectRisk($stats)
    {
        $total = $stats['total'] ?? 0;

        if ($total > 50) return 'high';
        if ($total > 20) return 'medium';

        return 'low';
    }
}