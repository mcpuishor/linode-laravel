<?php

namespace Mcpuishor\LinodeLaravel;

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

    public function all()
    {
        $result = $this->transport->get($this->endpoint);

        return collect($result['data']['data'] ?? []);
    }

    public function get(int $instanceId): array
    {
        return $this->engineSelected
            ? $this->transport->get($this->endpoint . '/' . $instanceId)
            : throw new \Exception('Database engine not selected');
    }

    public function create(array $data): array
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }

        return $this->transport->post($this->endpoint, $data);
    }

    public function delete(int $instanceId): array
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }

        return $this->transport->delete($this->endpoint . '/' . $instanceId);
    }

    public function update(int $instanceId, array $data): array
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }

        return $this->transport->put($this->endpoint . '/' . $instanceId, $data);
    }

    public function suspend(int $instanceId): array
    {
        if (!$this->engineSelected) {
            throw new \Exception('Database engine not selected');
        }

        return $this->transport->post($this->endpoint . '/' . $instanceId . '/suspend');
    }
}
