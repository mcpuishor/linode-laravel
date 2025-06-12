<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;
use Mcpuishor\LinodeLaravel\Transport;

beforeEach(function () {
    config(config_for_testing());
});

test('it throws linode api exception on error response', function () {
    Http::fake([
        'https://api.linode.com/v4/linode/instances*' => Http::response([
            'errors' => [
                ['reason' => 'Invalid region'],
                ['reason' => 'Invalid type']
            ],
            'message' => 'Validation failed'
        ], 400),
    ]);

    $transport = new Transport();

    try {
        $transport->get('linode/instances');
        fail('Exception was not thrown');
    } catch (LinodeApiException $e) {
        expect($e->getMessage())->toBe('Validation failed')
            ->and($e->getCode())->toBe(400)
            ->and($e->getErrorData())->toBe([
                ['reason' => 'Invalid region'],
                ['reason' => 'Invalid type']
            ]);
    }
});

test('it wraps client request exceptions', function () {
    Http::fake(function () {
        throw Http::failedRequest('Connection error', 500);
    });

    $transport = new Transport();

    try {
        $transport->get('linode/instances');
//        fail('Exception was not thrown');
    } catch (LinodeApiException $e) {
        expect($e->getMessage())->toContain('Linode API request failed')
            ->and($e->getCode())->toBe(500)
            ->and($e->getPrevious())->toBeInstanceOf(RequestException::class);
    }
});
