<?php
namespace Mcpuishor\LinodeLaravel;

use Illuminate\Support\Collection;

class Regions
{
    protected Transport $transport;
    protected $endpoint = 'regions';
    public function __construct(
        Transport $transport
    ){
        $this->transport = $transport;
    }

    public function all(): Collection
    {
        $result = $this->transport->get($this->endpoint);

        return collect($result['data'] ?? [])->map(function ($item) {
            return ValueObject::fromArray($item);
        });
    }

    public function get(string $regionId): ValueObject
    {
        $result = $this->transport->get($this->endpoint . '/' . $regionId);
        return ValueObject::fromArray($result['data'] ?? []);
    }
}
