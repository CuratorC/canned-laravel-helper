<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\ServiceProvider;
use Storage;
use DB;
use Log;

class CannedServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // 注册 curl 容器
        $this->app->singleton('curl', function () {
            return new Client();
        });

        // 注册 oss 容器
        $this->app->singleton('oss', function () {
            return Storage::disk('oss');
        });

        // ide helper
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton(EasySms::class, function ($app) {
            return new EasySms(config('easy-sms'));
        });

        $this->app->alias(EasySms::class, 'easy-sms');
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

        // 解决索引长度问题
        Schema::defaultStringLength(191);

        // Passport 的路由
        Passport::routes();
        // access_token 过期时间
        Passport::tokensExpireIn(now()->addDays(1));
        // refreshTokens 过期时间
        Passport::refreshTokensExpireIn(now()->addDays(14));
    }
}