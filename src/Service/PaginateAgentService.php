<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AgentRepository;
use Symfony\Component\HttpFoundation\{RequestStack, JsonResponse};

class PaginateAgentService
{
    private int $limit = 20;
    private int $page;
    private $offset;
    private AgentRepository $agentRepository;
    private UserService $userService;

    public function __construct(
        AgentRepository $agentRepository,
        RequestStack $requestStack,
        UserService $userService
    ) {
        $this->agentRepository = $agentRepository;
        $this->userService = $userService;
        $this->page = max(1, (int)$requestStack->getCurrentRequest()->query->get('page', 1));
        $this->offset = ($this->page - 1) * $this->limit;
    }

    public function getAgents(): JsonResponse
    {
        return $this->buildResponse($this->agentRepository->findVisibleForUserPaginated($this->userService->getUser(), $this->limit + 1, $this->offset));
    }

    public function getMyAgents(): JsonResponse
    {
        return $this->buildResponse($this->agentRepository->findUserAgentsPaginated($this->userService->getUser(), $this->limit + 1, $this->offset));
    }

    private function buildResponse(array $agents): JsonResponse
    {
        $hasMore = count($agents) > $this->limit;

        if ($hasMore) {
            $agents = array_slice($agents, 0, $this->limit);
        }

        // переводим сущности в простой массив, чтобы можно было вернуть JSON
        $data = array_map(fn($agent) => [
            'id' => $agent->getId(),
            'name' => $agent->getName(),
            'descr' => $agent->getDescr(),
            'isPublic' => $agent->isPublic(),
            'username' => $agent->getUsername(),
            'createdAt' => $agent->getCreatedAt()->format('Y-m-d'),
        ], $agents);

        return new JsonResponse([
            'agents' => $data,
            'hasMore' => $hasMore
        ]);
    }
}
