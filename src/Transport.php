<?php

namespace Mcpuishor\LinodeLaravel;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Mcpuishor\LinodeLaravel\Exceptions\LinodeApiException;

class Transport
{
    /**
     * The HTTP client instance.
     */
    protected PendingRequest $http;

    /**
     * The base URL for the API.
     */
    protected string $baseUrl;

    /**
     * The API version.
     */
    protected string $apiVersion;

    /**
     * The request timeout in seconds.
     */
    protected int $timeout;

    /**
     * Create a new Transport instance.
     */
    public function __construct(string $apiKey = null)
    {
        $this->baseUrl = rtrim(config('linode.api_url'), '/');
        $this->apiVersion = config('linode.api_version');
        $this->timeout = config('linode.timeout', 30);
        $apiKey = $apiKey ?? config('linode.api_key');

        $this->http = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
        ])
            ->asJson()
            ->acceptJson()
            ->timeout($this->timeout);
    }

    /**
     * Send a GET request to the API.
     *
     * @param string $endpoint The API endpoint
     * @param array $query Optional query parameters
     * @return array The decoded response
     * @throws RequestException
     */
    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('get', $endpoint, ['query' => $query]);
    }

    /**
     * Send a POST request to the API.
     *
     * @param string $endpoint The API endpoint
     * @param array $data The request payload
     * @return array The decoded response
     * @throws RequestException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('post', $endpoint,$data ?  ['json' => $data] : null);
    }

    /**
     * Send a PUT request to the API.
     *
     * @param string $endpoint The API endpoint
     * @param array $data The request payload
     * @return array The decoded response
     * @throws RequestException
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('put', $endpoint, ['json' => $data]);
    }

    /**
     * Send a PATCH request to the API.
     *
     * @param string $endpoint The API endpoint
     * @param array $data The request payload
     * @return array The decoded response
     * @throws RequestException
     */
    public function patch(string $endpoint, array $data = []): array
    {
        return $this->request('patch', $endpoint, ['json' => $data]);
    }

    /**
     * Send a DELETE request to the API.
     *
     * @param string $endpoint The API endpoint
     * @return array The decoded response
     * @throws RequestException
     */
    public function delete(string $endpoint): array
    {
        return $this->request('delete', $endpoint);
    }

    /**
     * Send a request to the API.
     *
     * @param string $method The HTTP method
     * @param string $endpoint The API endpoint
     * @param array $options The request options
     * @return array The decoded response
     * @throws RequestException
     * @throws LinodeApiException
     */
    protected function request(string $method, string $endpoint, ?array $options = []): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = $this->http->$method($url, $options[$method === 'get' ? 'query' : 'json'] ?? null);

            if ($response->failed()) {
                throw new LinodeApiException(
                    $response->json('message', 'Linode API request failed ' . $response->status() . ' ' . $response),
                    $response
                );
            }

            return $this->parseResponse($response);
        } catch (RequestException $e) {
            throw new LinodeApiException(
                'Linode API request failed: ' . $e->getMessage(),
                $e->response,
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Build the full URL for an API endpoint.
     *
     * @param string $endpoint The API endpoint
     * @return string The full URL
     */
    protected function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        return "{$this->baseUrl}/{$this->apiVersion}/{$endpoint}";
    }

    /**
     * Parse the API response.
     *
     * @param Response $response The HTTP response
     * @return array The decoded response data
     */
    protected function parseResponse(Response $response): array
    {
        return $response->json() ?? [];
    }

    /**
     * Get the HTTP client instance.
     *
     * @return PendingRequest
     */
    public function getHttpClient(): PendingRequest
    {
        return $this->http;
    }

    /**
     * Set a custom HTTP client instance.
     *
     * @param PendingRequest $http
     * @return self
     */
    public function setHttpClient(PendingRequest $http): self
    {
        $this->http = $http;
        return $this;
    }
}

