<?php

namespace Mcpuishor\LinodeLaravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Mcpuishor\LinodeLaravel\ValueObject;

class AsValueObject implements CastsAttributes
{
    /**
     * Cast the given value from the database into a ValueObject instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return ValueObject
     */
    public function get($model, $key, $value, $attributes)
    {
        // Decode the JSON value into an array and pass to ValueObject
        return ValueObject::fromArray(json_decode($value, true) ?? []);
    }

    /**
     * Prepare the given value for storage in the database.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string|null
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value instanceof ValueObject) {
            // Convert the ValueObject back into a JSON string
            return $value->toJson();
        }

        if (is_array($value)) {
            // If an array is provided, convert it to a ValueObject and then to JSON
            return ValueObject::fromArray($value)->toJson();
        }

        return $value;
    }
}
