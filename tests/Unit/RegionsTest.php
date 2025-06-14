<?php

use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;
use Mcpuishor\LinodeLaravel\LinodeClient;
use Mcpuishor\LinodeLaravel\Regions;
use Mcpuishor\LinodeLaravel\ValueObject;

beforeEach(function () {
    config(config_for_testing());
});

describe('Regions client', function () {
    it('can get the client from facade', function () {
        $linode = LinodeClient::make();

        expect($linode)->toBeInstanceOf(LinodeClient::class)
            ->and($linode->regions())->toBeInstanceOf(Regions::class);
    });

    it('can get the client from app', function () {
        $linode = app(LinodeClient::class);

        expect($linode)->toBeInstanceOf(LinodeClient::class)
            ->and($linode->regions())->toBeInstanceOf(Regions::class);
    });
});

describe('Regions', function () {
    it('can get all regions', function () {
        $predefinedResponse = json_decode(file_get_contents(__DIR__ . '/../Fixtures/linode-regions.json'), true);
        Http::fake([
            'https://api.linode.com/v4/regions' => Http::response([
                'data' => $predefinedResponse
            ], 200),
        ]);

        $linode = app(LinodeClient::class);
        $regions = $linode->regions()->all();

        expect($regions)->toBeCollection()
            ->and($regions)->toHaveCount(2)
            ->and($regions->first())->toBeInstanceOf(ValueObject::class)
            ->and($regions->first()->toArray())->toHaveKeys([
                'id', 'label', 'country', 'capabilities', 'status', 'resolvers'
            ]);
    });

    it('can get a specific region by id', function () {
        $regionId = 'us-east';
        $predefinedResponse = json_decode(file_get_contents(__DIR__ . '/../Fixtures/linode-regions.json'), true);
        $regionData = $predefinedResponse[0]; // us-east region data

        Http::fake([
            "https://api.linode.com/v4/regions/{$regionId}" => Http::response([
                'data' => $regionData
            ], 200),
        ]);

        $linode = app(LinodeClient::class);
        $region = $linode->regions()->get($regionId);

        expect($region)->toBeInstanceOf(ValueObject::class)
            ->and($region->toArray())->toHaveKeys([
                'id', 'label', 'country', 'capabilities', 'status', 'resolvers'
            ])
            ->and($region->id)->toBe('us-east')
            ->and($region->label)->toBe('Newark, NJ');
    });

    it('throws an api exception if region not found', function () {
        $regionId = 'invalid-region';

        Http::fake([
            "https://api.linode.com/v4/regions/{$regionId}" => Http::response([], 404),
        ]);

        $linode = app(LinodeClient::class);

        expect(fn() => $linode->regions()->get($regionId))
            ->toThrow(LinodeApiException::class, 'Linode API request failed');
    });
});
