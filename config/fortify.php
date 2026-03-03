<?php

use Laravel\Fortify\Features;

return [
    'guard'      => 'web',
    'middleware' => ['web'],
    'prefix'     => '',
    'domain'     => null,
    'views'      => true,

    'home' => '/dashboard',   // ← tambahkan ini

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm'         => true,
            'confirmPassword' => true,
        ]),
    ],

    'limitTTL' => 900,
];
