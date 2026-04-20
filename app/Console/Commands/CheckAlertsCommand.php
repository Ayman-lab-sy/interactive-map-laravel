<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class CheckAlertsCommand extends Command
{
    protected $signature = 'media:check-alerts';
    protected $description = 'Check for alert conditions';

    public function handle()
    {
        $alert = app(\App\Services\AlertDetectionService::class)->detect();

        if (!$alert) {
            return;
        }

        // 🔥 نبني request وهمي
        $request = new Request([
            'type' => 'alert',
            'governorate' => $alert['governorate'],
            'days' => 1
        ]);

        // 🔥 توليد + نشر
        $post = app(\App\Services\MediaService::class)->generatePost($request);

        $imagePath = app(\App\Services\MediaImageService::class)->generate($request, $post);

        app(\App\Services\PublisherService::class)->publish($imagePath, $post);

        $this->info("Alert sent for {$alert['governorate']}");
    }
}