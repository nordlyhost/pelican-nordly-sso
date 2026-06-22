<?php

namespace Nordly\SsoLogin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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

            // Pelican's FilamentServiceProvider registers primary=Color::Blue globally.
            // Inject CSS custom property overrides after Filament's styles so the
            // Nordly green always wins regardless of color-manager registration order.
            FilamentView::registerRenderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => $this->brandingCss(),
            );

            // Replace Pelican footer copyright with Nordly's.
            FilamentView::registerRenderHook(
                'pelican::footer.start',
                fn (): string => '<span class="font-semibold">&copy; ' . date('Y') . ' Nordly. All rights reserved.</span>',
            );
        }
    }

    public function boot(Panel $panel): void {}

    private function brandingCss(): string
    {
        // oklch palette generated from #2d5f3f (Nordly boreal green)
        return '<style>' .
            ':root{' .
            '--primary-50:oklch(0.97717647058824 0.01395454545455 153.835);' .
            '--primary-100:oklch(0.95035294117647 0.03272727272727 153.835);' .
            '--primary-200:oklch(0.90547058823529 0.06318181818182 153.835);' .
            '--primary-300:oklch(0.84047058823529 0.10604545454546 153.835);' .
            '--primary-400:oklch(0.75352941176471 0.15027272727273 153.835);' .
            '--primary-500:oklch(0.68270588235294 0.17009090909091 153.835);' .
            '--primary-600:oklch(0.59782352941176 0.16913636363636 153.835);' .
            '--primary-700:oklch(0.51494117647059 0.14940909090909 153.835);' .
            '--primary-800:oklch(0.44611764705882 0.12331818181818 153.835);' .
            '--primary-900:oklch(0.39458823529412 0.09963636363636 153.835);' .
            '--primary-950:oklch(0.27788235294118 0.07136363636364 153.835);' .
            '}' .
            // Hide the default Pelican footer copyright link
            'footer a[href*="pelican.dev"]{display:none}' .
            '</style>';
    }
}
