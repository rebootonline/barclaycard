# Rebootonline Barclaycard API handler

A full-featured Barclaycard Direct link payment API class for PHP


## Class Features
* PSR-4 autoloading compliant structure
* Unit-Testing with PHPUnit
* Easy to use to any framework or even a plain php file
* Open Source
* Allows processing of optional Fields 
* Support for UTF-8 content and 8bit, base64, binary, and quoted-printable encodings
* Compatible with PHP 5.5 and later
* Namespaced to prevent name clashes
* Simple and minimal






## Installation & loading

You can clone the package directly in to your application and run ```bash  composer dump-autoload    ```

```bash

git clone https://github.com/rebootonline/Reboot-barclaycard.git

composer dump-autoload   

```

Use composer to install package in to your application


```bash

composer require rebootonline/barclaycard

```


## A Simple Example

```php
use Reboot\Barclaycard;


	/*

	Step 1 : Instantiate/create your object

	*/

	$payment = new Barclaycard($pspid,$user_name, $password, $pw, $payemntUrl);

	/*

	Step 2 : Call the amount, card and orderId methods chaining them together in order 
	Step 3 : Call the customer method for additional parameters 
	Step 4 : Call pay method and your done


	*/


$process=$payment->amount($amount, $currency)
		->card($expiry_date, $card_number, $card_name, $cvc)
		->orderId('abc123')
		->customer($email, $phone, $address_1,  $town, $postcode)
		->pay();
	/*

	To retrieve results call the response method

	*/				
	echo $process->response();
					

	/*

	Method 2: you can just pass parameters to pay method after  instantiate class
	then call response method to retrieve results.

	*/
$process=$payment->pay([
	'amount'=>'123',
	'currency'=>'gbp',
	'expiry_date'=>'10:10',
	'card_number'=>'698655445',
	'card_name'=>'bob',
	'cvc'=>'321',
	'order_id'=>'abc123',
	'email'=>'ali@rebootonoline.com',
	'phone'=>'079856422',
	'address'=>'123 st road',
	'town'=>'london',
	'postcode'=>'e23 6sd'

]);
echo $process->response();
```

## License

This class open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

