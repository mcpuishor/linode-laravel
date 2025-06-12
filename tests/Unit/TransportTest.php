<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Transport;

beforeEach(function () {
    config(config_for_testing());
});

test('it adds authentication headers to requests', function () {
    Http::fake();

    $transport = new Transport();
    $transport->get('linode/instance');

    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Authorization', 'Bearer test-api-key') &&
               $request->hasHeader('Content-Type', 'application/json') &&
               $request->hasHeader('Accept', 'application/json');
    });
});

test('it builds correct api url', function () {
    Http::fake();

    $transport = new Transport();
    $transport->get('linode/instance');

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://api.linode.com/v4/linode/instance';
    });
});

test('it properly sends get requests with query parameters', function () {
    Http::fake([
        'https://api.linode.com/v4/linode/instance*' => Http::response(['data' => []], 200),
    ]);

    $transport = new Transport();
    $transport->get(endpoint: 'linode/instance', query: ['page' => 1, 'page_size' => 25]);

    Http::assertSent(function (Request $request) {
        return $request->method() === 'GET' &&
               $request->url() === 'https://api.linode.com/v4/linode/instance?page=1&page_size=25' &&
               $request->data()['page'] === 1 &&
               $request->data()['page_size'] === 25;
    });
});

test('it properly sends post requests with json data', function () {
    Http::fake([
        'https://api.linode.com/v4/linode/instance' => Http::response(['id' => 123], 201),
    ]);

    $transport = new Transport();
    $result = $transport->post('linode/instance', ['type' => 'g6-standard-1', 'region' => 'us-east']);

    Http::assertSent(function (Request $request) {
        return $request->method() === 'POST' &&
               $request->url() === 'https://api.linode.com/v4/linode/instance' &&
               $request->data()['type'] === 'g6-standard-1' &&
               $request->data()['region'] === 'us-east';
    });

    expect($result)->toBe(['id' => 123]);
});

test('it properly sends put requests with json data', function () {
    Http::fake([
        'https://api.linode.com/v4/linode/instance/123' => Http::response(['id' => 123], 200),
    ]);

    $transport = new Transport();
    $transport->put('linode/instance/123', ['label' => 'updated-server']);

    Http::assertSent(function (Request $request) {
        return $request->method() === 'PUT' &&
               $request->url() === 'https://api.linode.com/v4/linode/instance/123' &&
               $request->data()['label'] === 'updated-server';
    });
});

test('it properly sends delete requests', function () {
    Http::fake([
        'https://api.linode.com/v4/linode/instance/123' => Http::response([], 200),
    ]);

    $transport = new Transport();
    $transport->delete('linode/instance/123');

    Http::assertSent(function (Request $request) {
        return $request->method() === 'DELETE' &&
               $request->url() === 'https://api.linode.com/v4/linode/instance/123';
    });
});

test('it allows custom http client', function () {
    Http::fake();

    $transport = new Transport();
    $customClient = Http::withHeaders(['X-Custom' => 'value'])->timeout(60);
    $transport->setHttpClient($customClient);

    expect($transport->getHttpClient())->toBe($customClient);
});
