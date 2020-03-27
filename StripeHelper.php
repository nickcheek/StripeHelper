<?php

namespace App\Helpers;

use Stripe\{Customer, Charge, Stripe, Token, Source, Event};
use\Stripe\Exception\{
    CardException,
    RateLimitException,
    InvalidRequestException,
    AuthenticationException,
    ApiConnectionException,
    ApiErrorException,
};

class StripeHelper
{

    protected $key;
    protected $secret;

    public function __construct()
    {
        $this->key = config('app.stripe_key');
        $this->secret = config('app.stripe_secret');
    }



    public function createCustomer(iterable $request): object
    {
        Stripe::setApiKey($this->secret);
        return $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone ?? '',
            'email' => $request->email ?? '',
            'address' => [
                'line1' => $request->address ?? '',
                'city' => $request->city ?? '',
                'postal_code' => $request->zip ?? ''
            ]
        ]);
    }

    public function createToken(iterable $request): object
    {
        Stripe::setApiKey($this->secret);
        $exp = explode('/', $request->month_year);
        return $token = Token::create([
            'card' => [
                'number'    => $request->card,
                'exp_month' => $exp[0],
                'cvc'       => $request->cvc,
                'exp_year'  => $exp[1],
            ],
        ]);
    }

    public function createPaymentSource(string $token, iterable $request): object
    {
        Stripe::setApiKey($this->secret);
        return Source::create([
            "type" => "card",
            "token" => $token['id'],
            "currency" => "usd",
            "owner" => [
                "email" => $request->email
            ]
        ]);
    }

    public function createCustomerSource(iterable $customer, object $payment_source): object
    {
        Stripe::setApiKey($this->secret);
        return Customer::createSource(
            $customer->id,
            ['source' => $payment_source['id']]
        );
    }

    public function createCharge(iterable $customer, iterable $request, $array = null,$descriptor): object
    {
        Stripe::setApiKey($this->secret);
        return Charge::create([
            'customer' => $customer->id,
            'amount'   => preg_replace('/[^0-9]/', '', $request->amount),
            'currency' => 'usd',
            'statement_descriptor' => $descriptor,
            'metadata' => $array
        ]);
    }

    public function setupCharge(iterable $request)
    {
        $customer = $this->createCustomer($request);
        $token = $this->createToken($request);
        $payment_source = $this->createPaymentSource($token, $request);
        $this->createCustomerSource($customer, $payment_source);
        return $customer;
    }

    public function chargeCustomer(iterable $customer, iterable $request, array $data = null, string $descriptor = null): object
    {
        $charge = $this->createCharge($customer, $request, $data, $descriptor);
        return $charge;
    }
}
