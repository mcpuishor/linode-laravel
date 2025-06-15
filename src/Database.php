<?php

namespace Mcpuishor\LinodeLaravel;

use Illuminate\Support\Collection;

class Database
{
    protected Transport $transport;
    protected $endpoint = 'databases/instances';
    protected $engineSelected = false;

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public function mysql():self
    {
        $this->endpoint = 'databases/mysql/instances';
        $this->engineSelected = true;
        return $this;
    }

    public function postgresql():self
    {
        $this->endpoint = 'databases/postgresql/instances';
        $this->engineSelected = true;
        return $this;
    }

    public function types(): Collection
    {
        $result = $this->transport->get('databases/types');

        return $this->mapToValueObjectsCollection($result['data'] ?? []);
    }

    public function type(string $typeId): ValueObject
    {
        $result = $this->transport->get('databases/types/' . $typeId);

        return ValueObject::fromArray($result ?? []);
    }

    public function all(): Collection
    {
        $result = $this->transport->get($this->endpoint);

        return $this->mapToValueObjectsCollection($result['data'] ?? []);
    }

    public function get(int $instanceId): ValueObject
    {
        return $this->engineSelected
            ? ValueObject::fromArray(
                $this->transport->get($this->endpoint . '/' . $instanceId)
            )
            : throw new \Exception('Database engine not selected');
    }

    public function create(array $data): ValueObject
    {
        return $this->engineSelected
            ? ValueObject::fromArray(
                $this->transport->post($this->endpoint, $data)
                ?? throw new \Exception('Database could not be created')
                )
            : throw new \Exception('Database engine not selected');
    }

    public function delete(int $instanceId): bool
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }
        $result = $this->transport->delete($this->endpoint . '/' . $instanceId);

        return $result === [];
    }

    public function update(int $instanceId, array $data): ValueObject
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }
        $result = $this->transport->put($this->endpoint . '/' . $instanceId, $data);

        return ValueObject::fromArray($result);
    }

    public function suspend(int $instanceId): bool
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }
        $result =  $this->transport->post($this->endpoint . '/' . $instanceId . '/suspend');

        return $result === [];
    }

    public function resume(int $instanceId): bool
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }
        $result =  $this->transport->post($this->endpoint . '/' . $instanceId . '/resume');

        return $result === [];
    }

    public function getCredentials(int  $instanceId): ValueObject
    {
        return $this->engineSelected
            ?  ValueObject::fromArray( $this->transport->get($this->endpoint .'/' .  $instanceId . '/credentials'))
            : throw new \Exception('Database engine not selected');
    }

    public function resetCredentials(int $instanceId): ValueObject
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }

        $this->transport->put($this->endpoint . '/' . $instanceId . '/credentials/reset');
        return $this->getCredentials($instanceId);
    }

    public function ssl(): ValueObject
    {
        return $this->engineSelected
            ? ValueObject::fromArray(
                $this->transport->get($this->endpoint . '/ssl')
            )
            : throw new \Exception('Database engine not selected');
    }

    private function mapToValueObjectsCollection(array $data): Collection
    {
        return collect($data)->map(function ($item) {
            return ValueObject::fromArray($item);
        });
    }
}
