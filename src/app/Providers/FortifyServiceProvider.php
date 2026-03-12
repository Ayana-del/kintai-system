<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class FortifyServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::authenticateUsing(function (LoginRequest $request) {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'login_failed' => 'ログイン情報が登録されていません',
                ]);
            }

            return $user;
        });

        $this->app->bind(
            \Laravel\Fortify\Http\Requests\RegisterRequest::class,
            RegisterRequest::class
        );

        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            LoginRequest::class
        );
    }
}
