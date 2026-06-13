<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateLoginAttempt
{
    public function __invoke(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = User::where('phone', $request->email)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return null;
        }

        if (!config('app.device_restriction_enabled') || $user->isAdmin) {
            return $user;
        }

        $deviceToken = $request->input('device_token');

        if (is_null($user->device_token)) {
            if ($deviceToken) {
                $user->update(['device_token' => $deviceToken]);
            }
            return $user;
        }

        if ($deviceToken && $user->device_token !== $deviceToken) {
            throw ValidationException::withMessages([
                'email' => 'Perangkat ini tidak terdaftar. Hubungi administrator untuk mereset perangkat Anda.',
            ]);
        }

        return $user;
    }
}
