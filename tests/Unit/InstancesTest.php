<?php

use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;
use Mcpuishor\LinodeLaravel\Instances;
use Mcpuishor\LinodeLaravel\LinodeClient;
use Mcpuishor\LinodeLaravel\ValueObject;

beforeEach(function () {
    config(config_for_testing());
});

it('can get the client from facade', function () {
    $linode = LinodeClient::make();

    expect($linode)->toBeInstanceOf(LinodeClient::class)
        ->and($linode->instances())->toBeInstanceOf(Instances::class);
});

it('can get the client from app', function () {
    $linode = app(LinodeClient::class);

    expect($linode)->toBeInstanceOf(LinodeClient::class)
        ->and($linode->instances())->toBeInstanceOf(Instances::class);
});

it('can get all the instances', function () {
    $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-instances.json'), true);
    Http::fake([
        'https://api.linode.com/v4/linode/instances' => Http::response([
            'data' => $predefinedResponse, 200
        ]),
    ]);

    $linode = app(LinodeClient::class);
    $instances = $linode->instances()->all();

    expect($instances)->toBeCollection()
        ->and($instances)->toHaveCount(1)
        ->and($instances->first())->toBeInstanceOf(ValueObject::class)
        ->and($instances->first()->toArray())->toHaveKeys([
            'alerts', 'backups', 'capabilities', 'created', 'id', 'image', 'ipv4', 'ipv6',
            'label', 'region', 'specs', 'status', 'tags', 'type', 'updated', 'watchdog_enabled'
        ]);
});

it('can get an instance', function () {
    $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-instances.json'), true);
    $predefinedResponse = $predefinedResponse[0];

    Http::fake([
        'https://api.linode.com/v4/linode/instances/123' => Http::response($predefinedResponse, 200),
    ]);

    $linode = app(LinodeClient::class);
    $instance = $linode->instances()->get(123);

    expect($instance)->tobeInstanceOf(ValueObject::class)
        ->and($instance->toArray())->toHaveKeys([
            'id', 'label', 'status', 'created', 'updated', 'region', 'type',
            'ipv4', 'ipv6', 'image', 'specs', 'backups', 'tags'
        ]);
});

it('can create an instance', function () {
    $data = [
        'label' => 'test-instance',
        'region' => 'us-east',
        'type' => 'g6-standard-1',
        'image' => 'linode/ubuntu22.04',
    ];

    Http::fake([
        'https://api.linode.com/v4/linode/instances' => Http::response([
            'id' => 123,
            'label' => $data['label'],
            'region' => $data['region'],
            'type' => $data['type'],
            'image' => $data['image'],
            // other fields...
        ], 201),
    ]);

    $linode = app(LinodeClient::class);
    $instance = $linode->instances()->create($data);

    expect($instance)->tobeInstanceOf(ValueObject::class)
        ->and($instance->toArray())->toHaveKeys(['id', 'label', 'region', 'type', 'image']);
});

it('can update an instance', function () {
    $instanceId = 123;
    $data = [
        'label' => 'updated-instance',
        'region' => 'us-west',
    ];

    Http::fake([
        "https://api.linode.com/v4/linode/instances/{$instanceId}" => Http::response([
            'id' => $instanceId,
            'label' => $data['label'],
            'region' => $data['region'],
            // other fields...
        ], 200),
    ]);

    $linode = app(LinodeClient::class);
    $instance = $linode->instances()->update($instanceId, $data);

    expect($instance)->toBeInstanceOf(ValueObject::class)
        ->and($instance->toArray())->toHaveKeys(['id', 'label', 'region']);
});

it('can delete an instance', function () {
    $instanceId = 123;

    Http::fake([
        "https://api.linode.com/v4/linode/instances/{$instanceId}" => Http::response([], 200),
    ]);

    $linode = app(LinodeClient::class);
    $result = $linode->instances()->delete($instanceId);

    expect($result)->toBeTrue();
});

it('throws an exception if not found', function () {
    $instanceId = 999;

    Http::fake([
        "https://api.linode.com/v4/linode/instances/{$instanceId}" => Http::response([], 404),
    ]);

    $linode = app(LinodeClient::class);

    expect(fn() => $linode->instances()->get($instanceId))
        ->toThrow(LinodeApiException::class, 'Linode API request failed');
});
