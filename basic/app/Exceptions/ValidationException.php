<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    //
    public function render($request): \Illuminate\Http\JsonResponse
    {
        return response_error($this->getMessage());
    }
}
