<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use App\Entity\{Agent, User};
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FireworksApiService
{
    private $httpClient;
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->httpClient = HttpClient::create();
        $this->tokenStorage = $tokenStorage;
    }

    public function send(Agent $agent, $prompt, array $chatHistory = [])
    {
        $chatHistory[] = [
            'role' => 'user',
            'content' => preg_replace('/\s+/', ' ', trim($prompt))
        ];

        array_unshift($chatHistory, [
            'role' => 'system',
            'content' => $agent->getSystemPrompt()
        ]);

        $userApiKey = $this->getUser()->getFireworksApiKey();
        $apiKey = !empty($userApiKey) ? $userApiKey : $_ENV['DEFAULT_API_KEY'];

        $payload = [
            'model' => $_ENV['MODEL_NAME'],
            'max_tokens' => $agent->getMaxTokens(),
            'top_p' => $agent->getTopP(),
            'top_k' => $agent->getTopK(),
            'presence_penalty' => $agent->getPresencePenalty(),
            'frequency_penalty' => $agent->getFrequencyPenalty(),
            'temperature' => $agent->getTemperature(),
            'messages' => $chatHistory
        ];

        $response = $this->httpClient->request('POST', $_ENV['FIREWORKS_API_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        $data = json_decode($response->getContent(), true);
        return $data['choices'][0]['message']['content'] ?? '';
    }

    private function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
