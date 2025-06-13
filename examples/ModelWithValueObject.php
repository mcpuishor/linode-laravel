<?php

namespace Mcpuishor\LinodeLaravel\Examples;

use Illuminate\Database\Eloquent\Model;
use Mcpuishor\LinodeLaravel\Casts\AsValueObject;

class ModelWithValueObject extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => AsValueObject::class,
        'metadata' => AsValueObject::class,
    ];
}

// Example usage:
// $model = ModelWithValueObject::find(1);
// $value = $model->settings->some_setting; // Access a property from the ValueObject
// $model->metadata = ['key' => 'value']; // Assign an array that will be cast to ValueObject
