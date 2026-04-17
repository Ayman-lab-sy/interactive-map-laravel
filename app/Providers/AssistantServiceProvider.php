<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Assistant\AssistantEngine;
use App\Services\Assistant\KnowledgeRepository;
use App\Services\Assistant\UnansweredRepository;
use App\Services\Assistant\UnansweredReader;

class AssistantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Knowledge (read-only)
        $this->app->singleton(KnowledgeRepository::class, function () {
            return new KnowledgeRepository();
        });

        // Unanswered (read)
        $this->app->singleton(UnansweredReader::class, function () {
            return new UnansweredReader();
        });

        // Unanswered (write)
        $this->app->singleton(UnansweredRepository::class, function () {
            return new UnansweredRepository();
        });

        // Core Engine
        $this->app->singleton(AssistantEngine::class, function ($app) {
            return new AssistantEngine(
                $app->make(KnowledgeRepository::class)
            );
        });
    }

    public function boot(): void
    {
        // لا شيء هنا الآن
    }
}
