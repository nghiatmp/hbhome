<?php

namespace App\Services;

use Auth;

use App\Models\User;

use Socialite;

class AuthService
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function getMe()
    {
        $me = Auth::user();
        return [
            'id' => $me['id'],
            'full_name' => $me['full_name'],
            'role' => $me['role'],
        ];
    }
}
