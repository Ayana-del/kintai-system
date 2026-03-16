<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Cache\RateLimiting\Limit;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        $this->app->bind(FortifyLoginRequest::class, AdminLoginRequest::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            if (request()->is('admin/*')) {
                return view('admin.login');
            }
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

        $this->app->instance(LoginResponseContract::class, new class implements LoginResponseContract {
            public function toResponse($request)
            {
                $role = auth()->user()->role;
                $redirectPath = ($role === 1)
                    ? '/admin/stamp_correction_request/list'
                    : '/';

                return redirect()->intended($redirectPath);
            }
        });

        $this->app->instance(LogoutResponseContract::class, new class implements LogoutResponseContract {
            public function toResponse($request)
            {
                if ($request->is('admin/*')) {
                    return redirect('/admin/login');
                }
                return redirect('/login');
            }
        });
    }
}
