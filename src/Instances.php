<?php
namespace Mcpuishor\LinodeLaravel;

use Illuminate\Support\Collection;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;

class Instances
{
    protected Transport $transport;
    protected $endpoint = 'linode/instances';

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
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

        return $result
            ? ValueObject::fromArray($result)
            : throw new \Exception('Instance not found');
    }

    public function create(array $data): ValueObject
    {
        $result = $this->transport->post($this->endpoint, $data);

        return $result
            ? ValueObject::fromArray($result)
            : throw new \Exception('Failed to create instance');
    }

    public function update(int $instanceId, array $data): ValueObject
    {
        $result = $this->transport->put($this->endpoint . '/' . $instanceId, $data);

        return $result
            ? ValueObject::fromArray($result)
            : throw new \Exception('Failed to update instance');
    }

    public function delete(int $instanceId): bool
    {
      $result = $this->transport->delete($this->endpoint . '/' . $instanceId);

      return $result === [];
    }
}
