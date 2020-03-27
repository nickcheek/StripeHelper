# Laravel Stripe Helper File

Simplifies using stripe in your laravel project


## Prerequisites

You'll need a copy of stripe/stripe-php

```
composer require stripe/stripe-php
```

### Installing

Copy this file into \App\Helpers directory or which ever directory you prefer, you may need to change the namespace if it differs.


After that's complete, add these lines to your config/app.php file:

```
'stripe_key' => env('STRIPE_KEY', null),
'stripe_secret' => env('STRIPE_SECRET', null),
```

Then you'll want to add the following keys to your .env file:

```
STRIPE_KEY=pk_live_*********************
STRIPE_SECRET=sk_live_******************
```

### How to use

In the below example, data is an array/object of any metadata information you want to use, if not, leave it out. The statement descriptor is what you'd like it to appear as on the credit card statement.  It accepts a string and is limited to 42 characters.

The $info represents the customer data.  The fields are name, phone, email, address, city, zip

Feel free to add or change the code to accept whatever you need from the stripe API.

```php

$info = new stdClass;
$info->name = "My Name";
$info->email = "email@emailaddress.com";

$data = ['invoice' => '123456','name'=>$info->name];

$stripe = new StripeHelper();
$customer = $stripe->setupCharge($info);
$stripe->chargeCustomer($customer, $request, $data = null, $descriptor = null)
```

### Alternative use

Call the helper in your constructor and you can reference it from any function

```php
protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeHelper();
    }

    public function charge(Request $request)
    {
        $customer = $this->stripe->setupCharge($request);
        $charge = $this->stripe->chargeCustomer($customer, $request);
    }
```


## Contributing

Feel free to contribute as you please.


## Authors

* **Nicholas Cheek** - [PurpleBooth](https://github.com/nickcheek)


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

