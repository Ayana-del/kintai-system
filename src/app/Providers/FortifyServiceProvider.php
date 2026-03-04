<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
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
