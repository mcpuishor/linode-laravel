<?php
namespace Mcpuishor\LinodeLaravel;

use Illuminate\Contracts\Support\Jsonable;

final readonly class ValueObject implements Jsonable
{
    protected array $attributes;

    public function __construct(array $data)
    {
        $this->attributes = $data;
    }

    static public function fromArray(array $data): self
    {
        return new self($data);
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
    public function toJson($options = 0): string
    {
        return json_encode($this->attributes, $options);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }


}
