<?php

namespace App\Services;

use Illuminate\Http\Request;

class MediaService
{
    public function generatePost(Request $request)
    {
        if ($request->get('type') === 'report' && !$request->filled('range')) {
            $request->merge([
                'range' => 'yesterday'
            ]);
        }
        $stats = app(StatsService::class)->getStats($request);

        $narrative = app(NarrativeEngine::class)->analyze($stats);

        $request->merge([
            'type' => 'report',
            'schedule' => 'daily'
        ]);

        return app(ContentStrategyService::class)
            ->decide($stats, $request, $narrative);
    }
}