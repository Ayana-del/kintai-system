<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $role = Auth::user()->role;

        if ($role === 1) {
            return redirect()->intended('/admin/attendance/list');
        }

        return redirect()->intended('/');
    }
}
