<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AuthorizationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessTokenResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationsController extends AccessTokenController
{

    /**
     * @param ServerRequestInterface $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServerRequestInterface $request): JsonResponse
    {
        try {
            $parsedBody = $request->getParsedBody();

            $request = $request->withParsedBody(array(
                'phone' => $parsedBody['phone'],
                'password' => $parsedBody['password'],
                'verification_key' => $parsedBody['verification_key'],
                'verification_code' => $parsedBody['verification_code'],
                'grant_type' => 'user-social',
                'client_id' => config('auth.passport.clients.user.id'),
                'client_secret' => config('auth.passport.clients.user.secret'),
            ));

            $response = json_decode($this->issueToken($request)->content());
            return (new AccessTokenResource($response))->response()->setStatusCode(201);

        } catch (\Exception $exception) {
            throw new AuthorizationException("登录失败，用户名或密码错误");
        }
    }

    /**
     * 刷新令牌
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function update(ServerRequestInterface $request): Response
    {
        return $this->issueToken($request);
    }

    /**
     * 用户登出
     * @return Application|ResponseFactory|Response
     * @throws AuthenticationException
     */
    public function destroy(): Response|Application|ResponseFactory
    {
        if (auth('api')->check()) {
            auth('api')->user()->token()->revoke();
            return response(null, 204);
        } else {
            throw new AuthenticationException('The token is invalid.');
        }
    }
}
