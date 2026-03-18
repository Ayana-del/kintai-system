<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\Facades\Auth;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        if ($request->is('admin/*') || $request->is('admin/logout')) {
            return redirect('/admin/login');
        }

        return redirect('/login');
    }
}
