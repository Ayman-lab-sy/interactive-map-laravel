<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaImageController extends Controller
{
    public function generate(Request $request)
    {
        return app(\App\Services\MediaImageService::class)->generate($request);
    }
}