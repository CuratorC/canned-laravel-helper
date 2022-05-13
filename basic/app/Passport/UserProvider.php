<?php

namespace App\Passport;

use App\Exceptions\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ServerRequestInterface;
use Sk\Passport\UserProvider as BasicProvider;
use Illuminate\Auth\AuthenticationException;

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
            'password'  => 'required|string',
        ]);
    }

    /**
     * 验证
     * @param ServerRequestInterface $request
     * @return mixed|null
     * @throws ValidationException
     */
    public function retrieve(ServerRequestInterface $request)
    {
        $inputs = $this->only($request, [
            'phone',
            'password',
        ]);

        if (Auth::attempt($inputs)) {
            return User::where('phone', $inputs['phone'])->first();
        } else throw new ValidationException("密码错误");

    }
}