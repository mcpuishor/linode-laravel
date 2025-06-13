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
        if (!array_key_exists($name, $this->attributes)) {
            throw new \InvalidArgumentException("Property {$name} does not exist.");
        }

        return $this->attributes[$name];
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
    public function toJson($options = 0): string
    {
        return json_encode($this->attributes, $options);
    }
    public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }

    public function __toString(): string
    {
        return $this->toJson();
    }


}
