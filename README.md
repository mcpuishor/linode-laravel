# Linode Laravel

A Laravel 12 package for Linode integration.

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
// Usage examples will be added as features are implemented
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
