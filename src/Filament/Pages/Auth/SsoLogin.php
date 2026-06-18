<?php

namespace Nordly\SsoLogin\Filament\Pages\Auth;

use App\Filament\Pages\Auth\Login as PelicanLogin;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;

/**
 * Authentik-only login for the customer (app) panel.
 *
 * Extends Pelican's own login page and shows only the OAuth (Authentik) button.
 * The native username/password form is shown only for break-glass, reached at
 * /login?local=1 — the ?local flag is bound with #[Url] so it survives Livewire
 * re-renders (e.g. after a failed recovery login).
 *
 * As a safety net, if no SSO provider is enabled the native form is shown anyway,
 * so the panel can never be locked out by this plugin.
 */
class SsoLogin extends PelicanLogin
{
    #[Url]
    public ?string $local = null;

    public function form(Schema $schema): Schema
    {
        if ($this->showNativeForm()) {
            return parent::form($schema);
        }

        return $schema->components([
            $this->getOAuthFormComponent(),
        ]);
    }

    /**
     * Hide the "Sign in" submit action unless the native form is shown.
     *
     * @return array<\Filament\Actions\Action | \Filament\Actions\ActionGroup>
     */
    protected function getFormActions(): array
    {
        return $this->showNativeForm() ? parent::getFormActions() : [];
    }

    protected function showNativeForm(): bool
    {
        if (filled($this->local)) {
            return true;
        }

        // Fail open: if SSO isn't available for any reason, fall back to the
        // native form rather than rendering a login page with no way in.
        return ! isset($this->oauthService) || count($this->oauthService->getEnabled()) === 0;
    }
}
