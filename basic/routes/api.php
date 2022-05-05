<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')
    ->middleware([
        'throttle:' . config('api.rate_limits.sign'),
    ])
    ->name('api.v1.')
    ->group(function () {


        Route::middleware([
            'throttle:' . config('api.rate_limits.access'),
            'auth:api' // 用户验证
        ])
            ->group(function () {
                /*// 用户列表
                        Route::get('users', 'UsersController@index')->name('users.index');
                        // 用户详情
                        Route::get('users/{user}', 'UsersController@show')->name('users.show');*/
                //<------ route api↑
            });
    });

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found.'
    ], 404);
});