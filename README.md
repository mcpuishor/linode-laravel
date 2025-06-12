# Linode Laravel

A Laravel 12 package for Linode integration.

[![Tests](https://github.com/mcpuishor/linode-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/mcpuishor/linode-laravel/actions/workflows/tests.yml)

## Installation

You can install the package via composer:

```bash
composer require mcpuishor/linode-laravel
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag="linode-config"
```

This will publish a `linode.php` configuration file to your config directory.

Add your Linode API key to your `.env` file:

```
LINODE_API_KEY=your-api-key
```

## Usage

```php
// Basic usage example
$transport = app(\Mcpuishor\LinodeLaravel\Transport::class);

// Get all Linodes
$linodes = $transport->get('linodes');

// Create a new Linode
$newLinode = $transport->post('linodes', [
    'type' => 'g6-standard-1',
    'region' => 'us-east',
    'label' => 'my-new-linode'
]);
```

## Testing

Run the tests:

```bash
composer test
```

Run the tests with coverage report:

```bash
composer test:coverage
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
