<?php

use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;
use Mcpuishor\LinodeLaravel\Database;
use Mcpuishor\LinodeLaravel\LinodeClient;

beforeEach(function () {
    config(config_for_testing());
});

it('can get the client from facade', function () {
    $linode = LinodeClient::make();

    expect($linode)->toBeInstanceOf(LinodeClient::class)
        ->and($linode->databases())->toBeInstanceOf(Database::class);
});

it('can get the client from app', function () {
    $linode = app(LinodeClient::class);

    expect($linode)->toBeInstanceOf(LinodeClient::class)
        ->and($linode->databases())->toBeInstanceOf(Database::class);
});

it('can get all the databases', function () {
    $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-databases.json'), true);
    Http::fake([
        'https://api.linode.com/v4/databases/instances' => Http::response([
            'data' => $predefinedResponse, 200
        ]),
    ]);

    $linode = app(LinodeClient::class);
    $databases = $linode->databases()->all();

    expect($databases)->toBeCollection()
        ->and($databases)->toHaveCount(1)
        ->and($databases->first())->toBeArray()
        ->and($databases->first())->toHaveKeys([
            'id', 'label', 'status', 'created', 'updated', 'region', 'engine',
            'version', 'type', 'cluster_size', 'encrypted'
        ]);
});

it('can get a mysql database', function () {
    $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-databases.json'), true);
    $predefinedResponse = $predefinedResponse['data'][0];

    Http::fake([
        'https://api.linode.com/v4/databases/mysql/instances/123' => Http::response($predefinedResponse, 200),
    ]);

    $linode = app(LinodeClient::class);
    $database = $linode->databases()->mysql()->get(123);

    expect($database)->toBeArray()
        ->and($database)->toHaveKeys([
            'id', 'label', 'status', 'created', 'updated', 'region', 'engine',
            'version', 'type', 'cluster_size', 'encrypted'
        ]);
});

it('can get a postgresql database', function () {
    $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-databases.json'), true);
    $predefinedResponse = $predefinedResponse['data'][0];
    $predefinedResponse['engine'] = 'postgresql';

    Http::fake([
        'https://api.linode.com/v4/databases/postgresql/instances/123' => Http::response($predefinedResponse, 200),
    ]);

    $linode = app(LinodeClient::class);
    $database = $linode->databases()->postgresql()->get(123);

    expect($database)->toBeArray()
        ->and($database)->toHaveKeys([
            'id', 'label', 'status', 'created', 'updated', 'region', 'engine',
            'version', 'type', 'cluster_size', 'encrypted'
        ]);
});

it('throws an exception when getting a database without selecting an engine', function () {
    $linode = app(LinodeClient::class);

    expect(fn() => $linode->databases()->get(123))
        ->toThrow(\Exception::class, 'Database engine not selected');
});

it('can create a mysql database', function () {
    $data = [
        'label' => 'test-database',
        'region' => 'us-east',
        'type' => 'g6-standard-1',
        'engine_version' => '8.0.26',
        'cluster_size' => 3,
        'encrypted' => true,
    ];

    Http::fake([
        'https://api.linode.com/v4/databases/mysql/instances' => Http::response([
            'id' => 123,
            'label' => $data['label'],
            'region' => $data['region'],
            'type' => $data['type'],
            'engine' => 'mysql',
            'version' => $data['engine_version'],
            'cluster_size' => $data['cluster_size'],
            'encrypted' => $data['encrypted'],
            // other fields...
        ], 201),
    ]);

    $linode = app(LinodeClient::class);
    $database = $linode->databases()->mysql()->create($data);

    expect($database)->toBeArray()
        ->and($database)->toHaveKeys(['id', 'label', 'region', 'type', 'engine', 'version', 'cluster_size', 'encrypted']);
});

it('throws an exception when creating a database without selecting an engine', function () {
    $linode = app(LinodeClient::class);
    $data = ['label' => 'test-database'];

    expect(fn() => $linode->databases()->create($data))
        ->toThrow(\Exception::class, 'Database engine not selected');
});

it('can update a mysql database', function () {
    $databaseId = 123;
    $data = [
        'label' => 'updated-database',
        'allow_list' => ['192.0.2.1/32'],
    ];

    Http::fake([
        "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}" => Http::response([
            'id' => $databaseId,
            'label' => $data['label'],
            'allow_list' => $data['allow_list'],
            'engine' => 'mysql',
            // other fields...
        ], 200),
    ]);

    $linode = app(LinodeClient::class);
    $database = $linode->databases()->mysql()->update($databaseId, $data);

    expect($database)->toBeArray()
        ->and($database)->toHaveKeys(['id', 'label', 'allow_list', 'engine']);
});

it('throws an exception when updating a database without selecting an engine', function () {
    $linode = app(LinodeClient::class);
    $data = ['label' => 'updated-database'];

    expect(fn() => $linode->databases()->update(123, $data))
        ->toThrow(\Exception::class, 'Database engine not selected');
});

it('can delete a mysql database', function () {
    $databaseId = 123;

    Http::fake([
        "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}" => Http::response([], 200),
    ]);

    $linode = app(LinodeClient::class);
    $result = $linode->databases()->mysql()->delete($databaseId);

    expect($result)->toBeArray();
});

it('throws an exception when deleting a database without selecting an engine', function () {
    $linode = app(LinodeClient::class);

    expect(fn() => $linode->databases()->delete(123))
        ->toThrow(\Exception::class, 'Database engine not selected');
});

it('can suspend a mysql database', function () {
    $databaseId = 123;

    Http::fake([
        "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}/suspend" => Http::response([], 200),
    ]);

    $linode = app(LinodeClient::class);
    $result = $linode->databases()->mysql()->suspend($databaseId);

    expect($result)->toBeArray()
        ->and($result)->toBeEmpty();
});

it('throws an exception when suspending a database without selecting an engine', function () {
    $linode = app(LinodeClient::class);

    expect(fn() => $linode->databases()->suspend(123))
        ->toThrow(\Exception::class, 'Database engine not selected');
});

it('can resume a mysql database', function () {
    $databaseId = 123;

    Http::fake([
        "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}/resume" => Http::response([], 200),
    ]);

    $linode = app(LinodeClient::class);
    $result = $linode->databases()->mysql()->resume($databaseId);

    expect($result)->toBeArray()
        ->and($result)->toBeEmpty();
});

it('throws an exception when resuming a database without selecting an engine', function () {
    $linode = app(LinodeClient::class);

    expect(fn() => $linode->databases()->resume(123))
        ->toThrow(\Exception::class, 'Database engine not selected');
});

it('throws an api exception if database not found', function () {
    $databaseId = 999;

    Http::fake([
        "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}" => Http::response([], 404),
    ]);

    $linode = app(LinodeClient::class);

    expect(fn() => $linode->databases()->mysql()->get($databaseId))
        ->toThrow(LinodeApiException::class, 'Linode API request failed');
});
