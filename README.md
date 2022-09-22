## Short instruction
composer require spuniter/php-uniter-package
php artisan php-uniter:register {email} {password}

// edit .env file
// PHP_UNITER_ACCESS_TOKEN= token from your mail
// PROJECT_DIRECTORY= path to project root

php artisan php-uniter:generate {filePath}

## Installation

You can install the package via composer:

```bash
composer require  spuniter/php-uniter-package
```
## Testing
To run tests in package, you need to add
"PhpUniter\\PackageLaravel\\Tests\\": "vendor/spuniter/php-uniter-package/tests"
to   "autoload-dev": "psr-4" section of your composer.json

## Usage

### User registration:
```php
php artisan php-uniter:register {email} {password}
```
User will be registered, and access token will be sent to your email. You need to put that token to yours .env file as PHP_UNITER_ACCESS_TOKEN. After that you can send test generation queries.

### Package settings:

First, you need to set config values to you .env file:

```bash
PHP_UNITER_ACCESS_TOKEN=***
```
Access token sent after registration to your email.

Other options you may remain as defaults. Check that unit tests directory (defaults storage/tests/Unit) exists and is writable.

```bash
PHP_UNITER_BASE_URL=https://uniter1.tech
```
This is web address for our service. Use https://uniter1.tech

```bash
PHP_UNITER_OBFUSCATE=true
```
Obfuscation option. Set it to false if you need no obfuscation for you code.
```bash
PHP_UNITER_UNIT_TEST_BASE_CLASS=PHPUnit\Framework\TestCase
```
Base framework for yours test class. 
```bash
PHP_UNITER_UNIT_TESTS_DIRECTORY=tests/Unit
```
Base directory to save generated test classes. Check that directory exists and is writable.

### Test generation
```bash
php artisan php-uniter:generate {filePath}
```
Your class you want to test will be read from {filePath}, obfuscated if you did not turn obfuscation off, sent to our service. There will be created some phpunit test file to test different variants of yours class methods. The result will be deobfuscated and saved to PHP_UNITER_UNIT_TESTS_DIRECTORY - to some nested folder according to class namespace.

Open it, read it and use to test yours class.

Generated class, possibly, will not be completed test - read comments before each generated test method.

### Testing

You find package tests in package folder tests.

### Security

If you discover any security related issues, please email eduard.fomin@uniter1.tech instead of using the issue tracker.

## Credits

-   [PhpUniter](https://github.com/php-uniter)
