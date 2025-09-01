<?php

namespace App\Service;

use App\Dto\UpdateFireworksApiKeyDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdateFireworksApiKeyService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly UserService $userService
    ) {}

    public function updateApiKey(UpdateFireworksApiKeyDto $updateFireworksApiKeyDto): JsonResponse
    {
        $this->userService->getUser()->setFireworksApiKey($updateFireworksApiKeyDto->apiKey);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 200, 'message' => 'Data saved']);
    }
}
