<?php

namespace Nordly\SsoLogin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Nordly\SsoLogin\Filament\Pages\Auth\SsoLogin;

/**
 * Makes the customer-facing "app" panel login Authentik-only (SSO).
 *
 * Only the `app` panel is changed. The `admin` and `server` panels keep
 * Pelican's native login form, so an administrator always has a password path
 * and can never be locked out by this plugin.
 */
class NordlySsoPlugin implements Plugin
{
    public function getId(): string
    {
        return 'nordly-sso';
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() === 'app') {
            $panel->login(SsoLogin::class)
                  ->colors(['primary' => Color::hex('#2d5f3f')]);
        }
    }

    public function boot(Panel $panel): void {}
}
