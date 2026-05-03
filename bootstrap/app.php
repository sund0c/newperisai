<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Tambahkan SecurityHeaders ke grup web (BUKAN sebagai alias)
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);

        // Alias middleware
        $middleware->alias([
            'role'            => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'      => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role.or.perm'    => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            '2fa'             => \App\Http\Middleware\TwoFactorMiddleware::class,
            'password.expiry' => \App\Http\Middleware\PasswordExpiryMiddleware::class,
            'verify.auto-auth' => \App\Http\Middleware\EnsureUserIsAuthenticatedForVerification::class,
            //'sandidata' => \App\Http\Middleware\SandidataMiddleware::class,
            'maintenance.check' => \App\Http\Middleware\CheckMaintenance::class,
            'account.deletion' => \App\Http\Middleware\ProcessAccountDeletion::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
