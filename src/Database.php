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

    public function all(): Collection
    {
        $result = $this->transport->get($this->endpoint);

        return collect($result['data'] ?? [])->map(function ($item) {
            return ValueObject::fromArray($item);
        });
    }

    public function get(int $instanceId): ValueObject
    {
        $result = $this->transport->get($this->endpoint . '/' . $instanceId);

        return $this->engineSelected
            ? ValueObject::fromArray($result)
            : throw new \Exception('Database engine not selected');
    }

    public function create(array $data): ValueObject
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }
        $result = $this->transport->post($this->endpoint, $data);

        return $result
            ? ValueObject::fromArray($result)
            : throw new \Exception('Failed to create database');
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
}
