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

```php
// Get all databases
$databases = $linode->databases()->all();

// You must select a database engine (MySQL or PostgreSQL) before performing operations
// Get a specific MySQL database by ID
$database = $linode->databases()->mysql()->get(123);

// Get a specific PostgreSQL database by ID
$database = $linode->databases()->postgresql()->get(123);

// Create a new MySQL database
$newDatabase = $linode->databases()->mysql()->create([
    'label' => 'my-new-database',
    'region' => 'us-east',
    'type' => 'g6-standard-1',
    'engine_version' => '8.0.26',
    'cluster_size' => 3,
    'encrypted' => true,
]);

// Update a MySQL database
$updatedDatabase = $linode->databases()->mysql()->update(123, [
    'label' => 'updated-database',
    'allow_list' => ['192.0.2.1/32'],
]);

// Delete a MySQL database
$result = $linode->databases()->mysql()->delete(123);

// Suspend a MySQL database
$result = $linode->databases()->mysql()->suspend(123);
```

> **Note:** You must select a database engine (mysql or postgresql) before performing operations. Attempting operations without selecting an engine will throw an exception.

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
