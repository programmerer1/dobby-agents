<?php

namespace App\Service;

use App\Dto\UpdateFireworksApiKeyDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdateFireworksApiKeyService
{
    public function __construct(public readonly EntityManagerInterface $entityManager) {}

    public function updateApiKey(UpdateFireworksApiKeyDto $updateFireworksApiKeyDto, User $user): JsonResponse
    {
        $user->setFireworksApiKey($updateFireworksApiKeyDto->apiKey);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 200, 'message' => 'Data saved']);
    }
}
