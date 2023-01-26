<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // \URL::forceScheme('https'); // AWS（Cloud9）は初期が'http'なので必要
        // $this->app['request']->server->set('HTTPS','on'); // ペジネーションにも同じく
    }
}
