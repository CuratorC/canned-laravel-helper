<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class AccessTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    #[ArrayShape(['token_type' => "string", 'expires_in' => "integer", 'access_token' => "string", 'refresh_token' => "string"])] public function toArray($request): array
    {
        return [
            'token_type' => $this->token_type,
            'expires_in' => $this->expires_in,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
        ];
    }
}
