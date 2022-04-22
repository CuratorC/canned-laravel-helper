<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // 数据库监听
        /*DB::listen(function($query) {
            Log::info(
                $query->sql,
                $query->bindings,
            );
        });*/
        //
        Schema::defaultStringLength(191); // 解决索引长度问题
    }
}
