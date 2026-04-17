<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Services\Assistant\AssistantEngine;
use App\Services\Assistant\KnowledgeRepository;
use App\Services\Assistant\UnansweredRepository;
use App\Http\Controllers\Api\AssistantSearchController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/assistant/search', [AssistantSearchController::class, 'search']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/stats', [EventController::class, 'stats']);
Route::get('/events/summary', [EventController::class, 'summary']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::delete('/events/{id}', [EventController::class, 'destroy']);
Route::put('/events/{id}', [EventController::class, 'update']);

/*Route::post('/assistant/search', function (Request $request) {

    $question = trim((string) $request->input('question', ''));

    if ($question === '') {
        return response()->json([
            'answer' => 'يرجى كتابة سؤال صالح.',
        ]);
    }

    // إنشاء المحرّك مع مصدر المعرفة
    $engine = new AssistantEngine(
        new KnowledgeRepository()
    );

    $result = $engine->handle($question);

    // تفسير القرار الصادر من AssistantEngine
     
    switch ($result['action']) {

        case 'local':
            return response()->json([
                'answer' => $result['answer'],
            ]);

        case 'server':
            // نفس السلوك القديم تمامًا
            $serverResponse = Http::post(
                env('ASSISTANT_API_URL'),
                ['question' => $result['question']]
            )->json();

            return response()->json($serverResponse);

        case 'unanswered':
            // تسجيل السؤال غير المفهوم (كما هو حاليًا)
            // سيتم تحسينه لاحقًا عبر UnansweredRepository
            $unansweredRepo = new UnansweredRepository();
            $unansweredRepo->log(
                $result['question'],
                $result['expanded']
            );

            return response()->json([
                'answer' => 'عذرًا، لم أفهم سؤالك. يمكنك المحاولة بصيغة أخرى.',
            ]);
    }

    // Fallback أمني
    return response()->json([
        'answer' => 'حدث خطأ غير متوقع.',
    ]);
});*/