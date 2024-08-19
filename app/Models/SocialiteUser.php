<?php

namespace App\Models;

use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser as ParentUser;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @property int $user_id
 * @property string $provider
 * @property int $provider_id
 */
class SocialiteUser extends ParentUser
{
    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable
    {
        assert($this->user instanceof Authenticatable);

        return $this->user;
    }

    public static function findForProvider(string $provider, SocialiteUserContract $oauthUser): ?self
    {
        $user = self::query()
            ->where('provider', $provider)
            ->where('provider_id', $oauthUser->getId())
            ->first();

        return $user;
    }

    public static function createForProvider(string $provider, SocialiteUserContract $oauthUser, Authenticatable $user): self
    {
        return self::query()
            ->create([
                'user_id' => $user->getKey(),
                'provider' => $provider,
                'provider_id' => $oauthUser->getId(),
            ]);
    }
}
