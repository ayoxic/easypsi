<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureLocalizedEmailIsVerified::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            $locale = $request->route('locale') ?? app()->getLocale() ?? 'fr';

            return route('login.locale', ['locale' => $locale]);
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
