<?php

namespace Modules\Help\Providers;

use Illuminate\Support\ServiceProvider;

class HelpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Help');
    }

    public function register()
    {
        //
    }
}