<?php

namespace App\Services;

use Illuminate\Http\Request;

class MediaService
{
    public function generatePost(Request $request)
    {
        $stats = app(StatsService::class)->getStats($request);

        return app(ContentStrategyService::class)
            ->decide($stats, $request);
    }
}