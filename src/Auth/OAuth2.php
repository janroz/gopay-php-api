<?php

namespace GoPay\Auth;

use GoPay\Http\Browser;
use GoPay\Http\GopayBrowser;

class OAuth2
{
    private $config;
    private $browser;
    private $cache;

    public function __construct(array $config, TokenCache $c, Browser $b)
    {
        $this->config = $config;
        $this->cache = $c;
        $this->browser = new GopayBrowser($config, $b);
    }

    public function getAccessToken()
    {
        $scope = $this->config['scope'];
        $this->cache->setScope($scope);
        if ($this->cache->isExpired()) {
            $this->authorize($scope);
        }
        return $this->cache->getAccessToken();
    }

    private function authorize($scope)
    {
        $response = $this->browser->api(
            'oauth2/token',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => [$this->config['clientID'], $this->config['clientSecret']]
            ],
            ['grant_type' => 'client_credentials', 'scope' => $scope]
        );
        if ($response->hasSucceed()) {
            $accessToken = $response->json['access_token'];
            $expirationDate = new \DateTime("now + {$response->json['expires_in']} seconds");
            $this->cache->setAccessToken($accessToken, $expirationDate);
        }
    }
}
