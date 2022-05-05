<?php

namespace CuratorC\CannedLaravelHelper;

use Illuminate\Support\ServiceProvider as BasicServiceProvider;

class ServiceProvider extends BasicServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../basic' => app_path() . '/../',
        ]);
    }


    /**
     * Register services.
     * @return void
     */
    public function register()
    {
        // 注册 curl 容器
        /*$this->app->singleton('vpc-curl', function () {
            return new Client();
        });*/
    }
}