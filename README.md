# Ateros Pay

## Deprecation notice

Deprecated. Use [https://github.com/AterosProjects/ateros-pay-laravel](https://github.com/AterosProjects/ateros-pay-laravel).

## About
Ateros Pay is a payment gateway that allows you to accept 
online payments coming from multiple payment processors 
(like PayPal, Stripe, or Coinpayments).

This is a Laravel plugin to interact with the 
payment gateway. The non-Laravel version can be found at :  
[https://github.com/AterosProjects/ateros-pay-client](https://github.com/anto2oo/ateros-pay-client)

## Installation
Add this to your ```composer.json``` file :
```json
{
  "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/anto2oo/ateros-pay-laravel"
        }
    ]
}
```

Then just run :
```bash
composer require ateros/pay:dev-master
```

## Usage
The plugin should be auto-discovered by Laravel upon installation.
You may want to publish the config to set automatically an application token :
```bash
php artisan vendor:publish --tag=ateros-pay
```
The above command will create a ```pay.php``` file under your config directory.

## Examples
See [https://github.com/anto2oo/ateros-pay-client/tree/master/examples](https://github.com/anto2oo/ateros-pay-client/tree/master/examples) 
for code snippets to learn to use the package
