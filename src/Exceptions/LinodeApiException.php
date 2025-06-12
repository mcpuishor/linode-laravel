<?php

namespace Mcpuishor\LinodeLaravel\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class LinodeApiException extends Exception
{
    /**
     * The HTTP response from the API.
     */
    protected ?Response $response;

    /**
     * The error data from the API response.
     */
    protected array $errorData = [];

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param Response|null $response
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = "", ?Response $response = null, int $code = 0, Exception $previous = null)
    {
        $this->response = $response;

        if ($response) {
            $this->errorData = $response->json('errors', []);
            $statusCode = $response->status();

            // If no message was provided, try to get one from the response
            if (empty($message)) {
                $message = $response->json('message', 'An error occurred with the Linode API');
            }

            // Use the HTTP status code if no code was provided
            if ($code === 0) {
                $code = $statusCode;
            }
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP response that caused this exception.
     *
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get the error data from the API response.
     *
     * @return array
     */
    public function getErrorData(): array
    {
        return $this->errorData;
    }
}
