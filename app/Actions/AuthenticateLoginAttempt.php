<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateLoginAttempt
{
    public function __invoke(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('phone', $request->email)->first();
        }

        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }
    }
}
