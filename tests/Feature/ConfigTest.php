<?php

use Illuminate\Support\Facades\Config;

it('has linode configuration', function () {
    expect(Config::has('linode'))->toBeTrue();
    expect(Config::has('linode.api_key'))->toBeTrue();
    expect(Config::has('linode.api_version'))->toBeTrue();
    expect(Config::has('linode.api_url'))->toBeTrue();
    expect(Config::has('linode.timeout'))->toBeTrue();
});

it('loads test api key in test environment', function () {
    expect(Config::get('linode.api_key'))->toBe('test-api-key');
});

it('has correct default values', function () {
    expect(Config::get('linode.api_version'))->toBe('v4');
    expect(Config::get('linode.api_url'))->toBe('https://api.linode.com');
    expect(Config::get('linode.timeout'))->toBe(30);
});
