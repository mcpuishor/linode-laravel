<?php

use Illuminate\Database\Eloquent\Model;
use Mcpuishor\LinodeLaravel\Casts\AsValueObject;
use Mcpuishor\LinodeLaravel\ValueObject;

test('it casts json to value object when getting', function () {
    $cast = new AsValueObject();
    $model = new class extends Model {};

    $result = $cast->get($model, 'data', '{"name":"test","value":123}', []);

    expect($result)->toBeInstanceOf(ValueObject::class)
        ->and($result->name)->toBe('test')
        ->and($result->value)->toBe(123);
});

test('it casts value object to json when setting', function () {
    $cast = new AsValueObject();
    $model = new class extends Model {};
    $valueObject = new ValueObject(['name' => 'test', 'value' => 123]);

    $result = $cast->set($model, 'data', $valueObject, []);

    expect($result)->toBe('{"name":"test","value":123}');
});

test('it casts array to json when setting', function () {
    $cast = new AsValueObject();
    $model = new class extends Model {};

    $result = $cast->set($model, 'data', ['name' => 'test', 'value' => 123], []);

    expect($result)->toBe('{"name":"test","value":123}');
});
