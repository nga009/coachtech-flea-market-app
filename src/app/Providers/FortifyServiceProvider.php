<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;

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
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ビューの設定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.email-verification-notice');
        });

        // バリデーション
        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

        // カスタムリダイレクトロジック
        Fortify::redirects('login', function () {
            $user = auth()->user();
            
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            
            if (!$user->profile) {
                return redirect()->route('profile.form');
            }
            
            return redirect()->route('items.index');
        });

        Fortify::redirects('register', function () {
            return redirect()->route('verification.notice');
        });

        Fortify::redirects('email-verification', function () {
            $user = auth()->user();
            
            if (!$user->profile) {
                return redirect()->route('profile.form');
            }
            
            return redirect()->route('items.index');
        });
    }
}
