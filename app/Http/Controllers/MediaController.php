<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Services\Formatters\TelegramFormatter;
use App\Services\TelegramService;
use App\Services\MediaImageService;
use App\Services\PublisherService;

class MediaController extends Controller
{
    public function generatePost(Request $request)
    {
        $content = app(\App\Services\MediaService::class)->generatePost($request);

        return response()->json([
            'post' => $content
        ]);
    }

    public function map(Request $request)
    {
        return app(\App\Services\MapRendererService::class)->render($request);
    }

    public function publish(Request $request)
    {
        $post = app(MediaService::class)->generatePost($request);

        $imagePath = app(MediaImageService::class)->generate($request, $post);

        $channels = $request->get('channels', ['telegram']);

        app(PublisherService::class)->publish($imagePath, $post, $channels);

        return response()->json([
            'status' => 'sent',
            'channels' => $channels,
            'post' => $post
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}