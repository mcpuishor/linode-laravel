<?php
namespace Mcpuishor\LinodeLaravel;

use Illuminate\Contracts\Support\Jsonable;

final readonly class ValueObject implements Jsonable
{
    protected array $attributes;
    protected bool $silentOnNonExistingAttributes;

    public function __construct(array $data, bool $silentOnNonExistingAttributes = false)
    {
        $this->attributes = $data;
        $this->silentOnNonExistingAttributes = $silentOnNonExistingAttributes;
    }

    static public function fromArray(array $data): self
    {
        return new self($data);
    }

    protected function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function __get($name)
    {
        $value = $this->attributes[$name] ?? null;

        if (is_array($value) && $this->isAssoc($value)) {
            return new self($value);
        }

        return $value;
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
