<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest AS BasicRequest;

class FormRequest extends BasicRequest
{
    public function authorize(): bool
    {
        // Using policy for Authorization
        return true;
    }
}

