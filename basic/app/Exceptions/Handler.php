<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // 远程服务器 500 错误
        $this->renderable(function (\GuzzleHttp\Exception\ServerException $exception, $request) {
            return $this->responseJsonFromExceptionMessage($exception);
        });

        // 远程服务器 405 错误
        $this->renderable(function (\GuzzleHttp\Exception\ClientException $exception, $request) {
            return $this->responseJsonFromExceptionMessage($exception);
        });

        // 本地 422 错误
        $this->renderable(function (\Illuminate\Validation\ValidationException $exception, $request) {
            $this->putMessageByErrors($exception);
        });
    }

    /**
     *ㅤ根据 errors 拼接 message
     * @param \Illuminate\Validation\ValidationException $exception
     * @date 2020/10/19
     * @author Curator
     */
    private function putMessageByErrors(\Illuminate\Validation\ValidationException $exception)
    {
        $errors = $exception->errors();
        $errorMessage = [];
        foreach ($errors as $error) {
            foreach ($error as $item) {
                $errorMessage[] = $item;
            }
        }
        $message = implode(" ", $errorMessage);
        $reflectionObject = new \ReflectionObject($exception);
        $reflectionObjectProp = $reflectionObject->getProperty('message');
        $reflectionObjectProp->setAccessible(true);
        $reflectionObjectProp->setValue($exception, $message);
    }

    private function responseJsonFromExceptionMessage($exception): \Illuminate\Http\JsonResponse
    {
        $message = json_decode($exception->getResponse()->getBody()->getContents());
        if (isset($message->message)) {
            $message->message = '远程服务器错误:' . $message->message;
            if (isset($message->errors)) {
                $message->message = '远程服务器错误:' . create_message_by_errors($message->errors);
            }
        }
        return response()->json($message, $exception->getCode());
    }
}
