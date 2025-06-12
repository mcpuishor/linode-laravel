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

There are two ways to get the LinodeClient instance:

```php
// Method 1: Using the static make() method
$linode = \Mcpuishor\LinodeLaravel\LinodeClient::make();

// Method 2: Resolving from the container
$linode = app(\Mcpuishor\LinodeLaravel\LinodeClient::class);
```

### Working with Instances

```php
// Get all instances
$instances = $linode->instances()->all();

// Get a specific instance by ID
$instance = $linode->instances()->get(123);

// Create a new instance
$newInstance = $linode->instances()->create([
    'label' => 'my-new-instance',
    'region' => 'us-east',
    'type' => 'g6-standard-1',
    'image' => 'linode/ubuntu22.04',
]);

// Update an instance
$updatedInstance = $linode->instances()->update(123, [
    'label' => 'updated-instance',
    'region' => 'us-west',
]);

// Delete an instance
$result = $linode->instances()->delete(123);
```

### Database Support

> **Note:** Database functionality is planned but not yet implemented in the current version.

### Advanced Usage

If you need more direct access to the Linode API, you can use the Transport class:

```php
// Get the Transport instance
$transport = app(\Mcpuishor\LinodeLaravel\Transport::class);

// Make a custom API request
$result = $transport->get('some/endpoint');
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
