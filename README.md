# Nordly SSO Login (Pelican plugin)

Makes the **customer (`app`) panel** login on [Pelican](https://pelican.dev)
**Authentik-only**: it hides the native username/password form and leaves just the
SSO button. Built for [Nordly](https://nordly.gg)'s Authentik hub-and-spoke SSO, so
auto-provisioned customers (who have no Pelican password) aren't shown a form that
can't work for them.

- Pelican v1.0.0-beta34 (Filament v4)
- Pairs with Boy132's `generic-oidc-providers` plugin (which provides the Authentik OAuth button)
- Only the `app` panel is changed — `admin` and `server` keep the native login

## Behaviour

| URL | Shows |
| --- | --- |
| `panel.example.com/login` | SSO button only |
| `panel.example.com/login?local=1` | SSO button **+** native username/password form (break-glass) |
| `/admin/login`, `/server/login` | Native form (unchanged) |

The `?local` flag is a Livewire `#[Url]` property, so it survives re-renders (a
failed recovery login won't make the form vanish). If **no** SSO provider is
enabled, the native form is shown anyway — the plugin fails open and can't lock
you out.

## How it works

The plugin's `register(Panel $panel)` swaps the `app` panel's login page for
`SsoLogin`, which extends Pelican's own `App\Filament\Pages\Auth\Login` and
overrides just `form()` (OAuth-only) and `getFormActions()` (no submit button)
unless break-glass is active.

## Install

```bash
cd /var/www/pelican
sudo -u www-data git clone https://github.com/nordlyhost/pelican-nordly-sso.git plugins/nordly-sso
sudo -u www-data php artisan p:plugin:install nordly-sso
sudo -u www-data php artisan optimize:clear
```

Then visit `panel.example.com/login` — it should show only the SSO button.

## Uninstall / recover

If anything looks wrong, restore the native login instantly:

```bash
cd /var/www/pelican
sudo -u www-data php artisan p:plugin:disable nordly-sso
sudo -u www-data php artisan optimize:clear
```

(or `/login?local=1` for one-off break-glass access without disabling).

## License

[MIT](LICENSE).
