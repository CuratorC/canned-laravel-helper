<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    //
    public function render($request): \Illuminate\Http\JsonResponse
    {
        return canned_response_error($this->getMessage());
    }
}
