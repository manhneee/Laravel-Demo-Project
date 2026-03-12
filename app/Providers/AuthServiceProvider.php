<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Category;
use App\Models\Label;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Ticket;
use App\Policies\CategoryPolicy;
use App\Policies\LabelPolicy;
use App\Policies\TicketPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        Ticket::class => TicketPolicy::class,
        Category::class => CategoryPolicy::class,
        Label::class => LabelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();
    }
}
