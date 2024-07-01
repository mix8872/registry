<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShield;
use Error;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BasePage;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class Login extends BasePage implements HasForms
{

    private const LOGIN_ENTPOINT = '/ipa/session/login_password';
    private const JSON_ENTPOINT = '/ipa/session/json';

    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    public function authenticate(): ?LoginResponse
    {
        // Implement rate limiting to protect against brute force attacks
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(
                    __('filament-panels::pages/auth/login.notifications.throttled.title', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => ceil($exception->secondsUntilAvailable / 60),
                    ])
                )
                ->body(
                    array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __(
                        'filament-panels::pages/auth/login.notifications.throttled.body',
                        [
                            'seconds' => $exception->secondsUntilAvailable,
                            'minutes' => ceil($exception->secondsUntilAvailable / 60),
                        ]
                    ) : null
                )
                ->danger()
                ->send();
            return null;
        }

        $data = $this->form->getState();
        $user = strip_tags($data['username']);
        $pass = strip_tags($data['password']);
        $loginRes = $this->ipaLogin($user, $pass);

        if (!$loginRes['success']) {
            $this->throwFailureValidationException();
        }

        $user = User::updateOrCreate([
            'email' => $loginRes['userData']['mail'][0],
        ], [
            'name' => $user,
            'password' => Hash::make($pass)
        ]);

        foreach ($loginRes['userData']['memberof_group'] as $role) {
            FilamentShield::createRole($role);
        }

        $user->syncRoles(...$loginRes['userData']['memberof_group']);

        Auth::login($user, $data['remember']);
        return app(LoginResponse::class);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('username')
                            ->label(__('Username'))
                            ->required()
                            ->autocomplete()
                            ->autofocus()
                            ->extraInputAttributes(['tabindex' => 1]),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    private function ipaLogin($user, $pass)
    {
        try {
            $jar = new \GuzzleHttp\Cookie\CookieJar;

            Http::withOptions([
                'ssl_key' => [env('IPA_CACERT')],
                'cookies' => $jar
            ])->withHeaders([
                'referer' => env('IPA_HOST') . '/ipa'
            ])->asForm()->post(env('IPA_HOST') . self::LOGIN_ENTPOINT, [
                'user' => $user,
                'password' => $pass,
            ]);

            $cookies = $jar->count() ? $jar->toArray()[0] : false;
        } catch (Error|Exception $e) {
            Log::error("IPA login failure: " . $e->getMessage());
            $this->throwFailureValidationException();
        }

        $isCookiesValid = static::validateCookies($cookies);

        return [
            'success' => $isCookiesValid,
            'jar' => $isCookiesValid ? $jar : false,
            'userData' => $isCookiesValid ? $this->getUserData($user, $jar) : null
        ];
    }

    private function getUserData(string $user, \GuzzleHttp\Cookie\CookieJar|null $jar)
    {
        $res = Http::withOptions([
            'ssl_key' => [env('IPA_CACERT')],
            'cookies' => $jar
        ])->withHeaders([
            'referer' => env('IPA_HOST') . '/ipa'
        ])->post(env('IPA_HOST') . self::JSON_ENTPOINT, [
            'method' => 'user_show/1',
            'params' => [
                [$user],
                [
                    'all' => true,
                    'version' => env('IPA_VERSION')
                ]
            ],
        ]);
        return ($r = $res->json('result')) ? $r['result'] : null;
    }

    private static function validateCookies($c)
    {
        $domain = ltrim(env('IPA_HOST', 'newipa.grechka.digital'), 'https://');
        return match (false) {
            isset($c['Name'], $c['Value'], $c['Domain']), $c['Name'] === 'ipa_session', $c['Domain'] === $domain => false,
            default => true,
        };
    }

    public function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

}
