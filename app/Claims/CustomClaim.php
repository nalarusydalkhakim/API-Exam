<?php

namespace App\Claims;

use CorBosman\Passport\AccessToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomClaim
{
    public function handle(AccessToken $token, $next)
    {
        $data = [];
        $user = User::find($token->getUserIdentifier());
        if ($user) {
            $data = $user->toArray();
        }

        foreach ($data as $key => $value) {
            $token->addClaim($key, $value);
        }

        return $next($token);
    }
}
