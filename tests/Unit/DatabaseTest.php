<?php

use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;
use Mcpuishor\LinodeLaravel\Database;
use Mcpuishor\LinodeLaravel\LinodeClient;
use Mcpuishor\LinodeLaravel\ValueObject;

beforeEach(function () {
    config(config_for_testing());
});

describe('Database client', function () {
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
});

describe('Database instances', function () {

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
            ->and($databases->first())->tobeInstanceOf(ValueObject::class)
            ->and($databases->first()->toArray())->toHaveKeys([
                'id', 'label', 'status', 'created', 'updated', 'region', 'engine',
                'version', 'type', 'cluster_size', 'encrypted'
            ]);
    });

    it('can get a mysql database', function () {
        $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-databases.json'), true);
        $predefinedResponse = $predefinedResponse[0];

        Http::fake([
            'https://api.linode.com/v4/databases/mysql/instances/123' => Http::response($predefinedResponse, 200),
        ]);

        $linode = app(LinodeClient::class);
        $database = $linode->databases()->mysql()->get(123);

        expect($database)->toBeInstanceOf(ValueObject::class)
            ->and($database->toArray())->toHaveKeys([
                'id', 'label', 'status', 'created', 'updated', 'region', 'engine',
                'version', 'type', 'cluster_size', 'encrypted'
            ]);
    });

    it('can get a postgresql database', function () {
        $predefinedResponse = json_decode(file_get_contents(__DIR__ .'/../Fixtures/linode-databases.json'), true);
        $predefinedResponse = $predefinedResponse[0];
        $predefinedResponse['engine'] = 'postgresql';

        Http::fake([
            'https://api.linode.com/v4/databases/postgresql/instances/123' => Http::response($predefinedResponse, 200),
        ]);

        $linode = app(LinodeClient::class);
        $database = $linode->databases()->postgresql()->get(123);

        expect($database)->tobeInstanceOf(ValueObject::class)
            ->and($database->toArray())->toHaveKeys([
                'id', 'label', 'status', 'created', 'updated', 'region', 'engine',
                'version', 'type', 'cluster_size', 'encrypted'
            ]);
    });

    it('throws an exception when getting a database without selecting an engine', function () {
        $linode = app(LinodeClient::class);

        expect(fn() => $linode->databases()->get(123))
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
});

describe('Operations: create', function () {

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

        expect($database)->tobeInstanceOf(ValueObject::class)
            ->and($database->toArray())->toHaveKeys(['id', 'label', 'region', 'type', 'engine', 'version', 'cluster_size', 'encrypted']);
    });

    it('throws an exception when creating a database without selecting an engine', function () {
        $linode = app(LinodeClient::class);
        $data = ['label' => 'test-database'];

        expect(fn() => $linode->databases()->create($data))
            ->toThrow(\Exception::class, 'Database engine not selected');
    });
});

describe('Operations: update', function () {
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

        expect($database)->toBeInstanceOf(ValueObject::class)
            ->and($database->toArray())->toHaveKeys(['id', 'label', 'allow_list', 'engine']);
    });

    it('throws an exception when updating a database without selecting an engine', function () {
        $linode = app(LinodeClient::class);
        $data = ['label' => 'updated-database'];

        expect(fn() => $linode->databases()->update(123, $data))
            ->toThrow(\Exception::class, 'Database engine not selected');
    });
});

describe('Operations: delete', function () {
    it('can delete a mysql database', function () {
        $databaseId = 123;

        Http::fake([
            "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}" => Http::response([], 200),
        ]);

        $linode = app(LinodeClient::class);
        $result = $linode->databases()->mysql()->delete($databaseId);

        expect($result)->toBeTrue();
    });

    it('throws an exception when deleting a database without selecting an engine', function () {
        $linode = app(LinodeClient::class);

        expect(fn() => $linode->databases()->delete(123))
            ->toThrow(\Exception::class, 'Database engine not selected');
    });
});

describe('Operations: un/suspend', function () {
    it('can suspend a mysql database', function () {
        $databaseId = 123;

        Http::fake([
            "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}/suspend" => Http::response([], 200),
        ]);

        $linode = app(LinodeClient::class);
        $result = $linode->databases()->mysql()->suspend($databaseId);

        expect($result)->toBeTrue();
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

        expect($result)->toBeTrue();
    });

    it('throws an exception when resuming a database without selecting an engine', function () {
        $linode = app(LinodeClient::class);

        expect(fn() => $linode->databases()->resume(123))
            ->toThrow(\Exception::class, 'Database engine not selected');
    });
});

describe('Database Credentials', function () {
    it('can get credentials for a mysql database', function () {
        $databaseId = 123;
        $credentials = [
            'username' => 'linroot',
            'password' => 'someSecurePassword123!',
        ];

        Http::fake([
            "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}/credentials" => Http::response($credentials, 200),
        ]);

        $linode = app(LinodeClient::class);
        $result = $linode->databases()->mysql()->getCredentials($databaseId);

        expect($result)->toBeInstanceOf(ValueObject::class)
            ->and($result->toArray())->toHaveKeys([
                'username', 'password'
            ]);
    });

    it('throws an exception when getting credentials without selecting an engine', function () {
        $linode = app(LinodeClient::class);

        expect(fn() => $linode->databases()->getCredentials(123))
            ->toThrow(\Exception::class, 'Database engine not selected');
    });

    it('can reset credentials for a mysql database', function () {
        $databaseId = 123;
        $newCredentials = [
            'username' => 'linroot',
            'password' => 'newSecurePassword456!',
        ];

        Http::fake([
            "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}/credentials/reset" => Http::response([], 200),
            "https://api.linode.com/v4/databases/mysql/instances/{$databaseId}/credentials" => Http::response($newCredentials, 200),
        ]);

        $linode = app(LinodeClient::class);
        $result = $linode->databases()->mysql()->resetCredentials($databaseId);

        expect($result)->toBeInstanceOf(ValueObject::class)
            ->and($result->toArray())->toHaveKeys([
                'username', 'password'
            ])
            ->and($result->password)->toBe('newSecurePassword456!');
    });

    it('throws an exception when resetting credentials without selecting an engine', function () {
        $linode = app(LinodeClient::class);

        expect(fn() => $linode->databases()->resetCredentials(123))
            ->toThrow(\Exception::class, 'Database engine not selected');
    });
});

describe('Database Types', function () {
    it('can get all database types', function () {
        $typesData = [
            [
                'id' => 'g6-standard-1',
                'label' => 'Standard 1',
                'memory' => 2048,
                'disk' => 25600,
                'transfer' => 4000,
                'vcpus' => 1,
                'price' => [
                    'hourly' => 0.015,
                    'monthly' => 10
                ]
            ],
            [
                'id' => 'g6-standard-2',
                'label' => 'Standard 2',
                'memory' => 4096,
                'disk' => 51200,
                'transfer' => 5000,
                'vcpus' => 2,
                'price' => [
                    'hourly' => 0.03,
                    'monthly' => 20
                ]
            ]
        ];

        Http::fake([
            'https://api.linode.com/v4/databases/instances/types' => Http::response([
                'data' => $typesData
            ], 200),
        ]);

        $linode = app(LinodeClient::class);
        $types = $linode->databases()->types();

        expect($types)->toBeCollection()
            ->and($types)->toHaveCount(2)
            ->and($types->first())->toBeInstanceOf(ValueObject::class)
            ->and($types->first()->toArray())->toHaveKeys([
                'id', 'label', 'memory', 'disk', 'transfer', 'vcpus', 'price'
            ]);
    });

    it('can get a specific database type by id', function () {
        $typeId = 'g6-standard-1';
        $typeData = [
            'id' => 'g6-standard-1',
            'label' => 'Standard 1',
            'memory' => 2048,
            'disk' => 25600,
            'transfer' => 4000,
            'vcpus' => 1,
            'price' => [
                'hourly' => 0.015,
                'monthly' => 10
            ]
        ];

        Http::fake([
            "https://api.linode.com/v4/databases/instances/types/{$typeId}" => Http::response([
                'data' => $typeData
            ], 200),
        ]);

        $linode = app(LinodeClient::class);
        $type = $linode->databases()->type($typeId);

        expect($type)->toBeInstanceOf(ValueObject::class)
            ->and($type->toArray())->toHaveKeys([
                'id', 'label', 'memory', 'disk', 'transfer', 'vcpus', 'price'
            ])
            ->and($type->id)->toBe('g6-standard-1')
            ->and($type->label)->toBe('Standard 1');
    });

});

