<?php
namespace Mcpuishor\LinodeLaravel;

use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;

class Instances
{
    protected Transport $transport;
    const ENDPOINT = 'linode/instances';

    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    public function all()
    {
        $result = $this->transport->get(self::ENDPOINT);

        return collect($result['data']['data'] ?? []);
    }

    public function get(int $instanceId): array
    {
        $result = $this->transport->get(self::ENDPOINT . '/' . $instanceId);

        if (!$result) {
            throw new \Exception('Instance not found');
        }

        return $result;
    }

    public function create(array $data): array
    {
        $result = $this->transport->post(self::ENDPOINT, $data);

        if (!$result) {
            throw new \Exception('Failed to create instance');
        }

        return $result;
    }

    public function update(int $instanceId, array $data): array
    {
        $result = $this->transport->put(self::ENDPOINT . '/' . $instanceId, $data);

        if (!$result) {
            throw new \Exception('Failed to update instance');
        }

        return $result;
    }

    public function delete(int $instanceId): bool
    {
      $this->transport->delete(self::ENDPOINT . '/' . $instanceId);

      return true;
    }
}
