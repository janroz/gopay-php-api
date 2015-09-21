<?php

namespace GoPay;

use GoPay\Http\Browser;
use GoPay\Http\GopayBrowser;
use GoPay\Auth\OAuth2;

class Payments
{
    private $auth;
    private $browser;

    public function __construct(array $config, OAuth2 $a, Browser $b)
    {
        $this->browser = new GopayBrowser($config, $b);
        $this->auth = $a;
    }

    public function createPayment(array $payment)
    {
        return $this->api('', Browser::JSON, $payment);
    }

    public function getStatus($id)
    {
        return $this->api("/{$id}", Browser::FORM);
    }

    public function refund($id, $amount)
    {
        return $this->api("/{$id}/refund", Browser::FORM, ['amount' => $amount]);
    }

    public function createRecurrencePayment(array $payment)
    {
        return $this->api('', Browser::JSON, $payment);
    }

    public function recurrenceOnDemand($id, array $payment)
    {
        return $this->api("/{$id}/create-recurrence", Browser::JSON, $payment);
    }

    public function recurrenceVoid($id)
    {
        return $this->api("/{$id}/void-recurrence", Browser::FORM, array());
    }

    public function createPreauthorizedPayment(array $payment)
    {
        return $this->api('', Browser::JSON, $payment);
    }

    public function preauthorizedCapture($id)
    {
        return $this->api("/{$id}/capture", Browser::FORM, array());
    }

    public function preauthorizedVoid($id)
    {
        return $this->api("/{$id}/void-authorization", Browser::FORM, array());
    }

    private function api($urlPath, $contentType, $data = null)
    {
        return $this->browser->api(
            "payments/payment{$urlPath}",
            [
                'Accept' => 'application/json',
                'Content-Type' => $contentType,
                'Authorization' => "Bearer {$this->auth->getAccessToken()}"
            ],
            $data
        );
    }
}
