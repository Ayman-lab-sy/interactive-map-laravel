<?php

namespace App\Providers;

use App\Models\CaseModel;
use App\Policies\CasePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        CaseModel::class => CasePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
