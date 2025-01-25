<?php

namespace App\Middleware;

class AuthMiddleware
{
    private string $authKey;

    public function __construct(string $authKey)
    {
        $this->authKey = $authKey;
    }

    public function handle(): bool
    {
        $headers = getallheaders();

        if (!isset($headers['X-Auth-Key']) || $headers['X-Auth-Key'] !== $this->authKey) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return false;
        }

        return true;
    }
}