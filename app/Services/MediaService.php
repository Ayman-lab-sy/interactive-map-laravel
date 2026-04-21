<?php

namespace App\Services;

use Illuminate\Http\Request;

class MediaService
{
    public function generatePost(Request $request)
    {
        $stats = app(StatsService::class)->getStats($request);

        $narrative = app(NarrativeEngine::class)->analyze($stats);

        return app(ContentStrategyService::class)
            ->decide($stats, $request, $narrative);
    }
}