<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class PublishMediaCommand extends Command
{
    protected $signature = 'media:publish {type}';
    protected $description = 'Publish media content automatically';

    public function handle()
    {
        $type = $this->argument('type');

        $request = new Request();

        if ($type === 'daily') {
            $request->merge(['schedule' => 'daily']);
        }

        if ($type === 'social') {
            $request->merge(['type' => 'social']);
        }

        // 🔥 تشغيل النظام كامل
        $post = app(\App\Services\MediaService::class)->generatePost($request);

        $imagePath = app(\App\Services\MediaImageService::class)->generate($request, $post);

        app(\App\Services\PublisherService::class)->publish($imagePath, $post);

        $this->info("Media published: {$type}");
    }
}