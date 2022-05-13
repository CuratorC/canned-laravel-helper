<?php

namespace App\Passport;

use App\Exceptions\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ServerRequestInterface;
use Sk\Passport\UserProvider as BasicProvider;
use Illuminate\Auth\AuthenticationException;
use Cache;

class UserProvider extends BasicProvider
{
    /**
     * 字段过滤
     * @param ServerRequestInterface $request
     * @return void
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function validate(ServerRequestInterface $request)
    {
        $this->validateRequest($request, [
            'phone' => 'required|phone:CN',
            'password' => 'required|string',
            'verification_key' => 'required|string',
            'verification_code' => 'required|integer',
        ]);
    }

    /**
     * 验证
     * @param ServerRequestInterface $request
     * @return mixed|null
     * @throws ValidationException
     * @throws AuthenticationException
     */
    public function retrieve(ServerRequestInterface $request)
    {
        $inputs = $this->only($request, [
            'phone',
            'password',
            'verification_key',
            'verification_code',
        ]);

        $verifyData = Cache::get($request->verification_key);

        if (!$verifyData) {
            throw new ValidationException("验证码已失效", 403);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        if (Auth::attempt($inputs)) {
            // 清除验证码缓存
            Cache::forget($request->verification_key);
            return User::where('phone', $inputs['phone'])->first();
        } else throw new AuthenticationException("密码错误");
    }
}