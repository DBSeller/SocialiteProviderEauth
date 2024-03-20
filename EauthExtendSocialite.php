<?php

namespace SocialiteProviders\Eauth;

use SocialiteProviders\Manager\SocialiteWasCalled;

class EauthExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled): void
    {
        $socialiteWasCalled->extendSocialite('eauth', Provider::class);
    }
}