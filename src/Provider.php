<?php

namespace Socialiteproviders\Eauth;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{

    public const IDENTIFIER = 'EAUTH';


    protected function getTokenUrl()
    {
        return $this->getInstanceUri() . 'oauth/token';
    }

    protected $fields = ['id', 'name', 'cpf'];

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getInstanceUri() . 'oauth/authorize', $state);
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getInstanceUri() . 'api/user/logado', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'            => $user['id'],
            'name'          => $user['name'],
            'email'         => $user['email'],
            'cpfcnpj'   => $user['cpfcnpj'] ?? null,
        ]);
    }

    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            RequestOptions::FORM_PARAMS => $this->getTokenFields($code),
        ]);
        $this->credentialsResponseBody = json_decode((string) $response->getBody(), true);
        return $this->parseAccessToken($response->getBody());
    }

    protected function getInstanceUri()
    {
        return $this->getConfig('instance_uri', env('EAUTH_API_URL', 'https://api.eauth.dbseller.com.br/'));
    }

    public static function additionalConfigKeys()
    {
        return ['instance_uri'];
    }
}
