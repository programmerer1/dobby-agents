<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Dto\UpdateFireworksApiKeyDto;
use App\Dto\UserPasswordChangeDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Service\UpdateFireworksApiKeyService;
use App\Service\UserPasswordChangeService;

final class ProfileController extends AbstractController
{
    #[Route(path: '/dashboard/profile', name: 'app_dashboard_profile', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('dashboard/profile.html.twig');
    }

    #[Route(
        path: '/dashboard/profile/update-fireworks-api-key',
        name: 'app_dashboard_update_fireworks_api_key',
        methods: ['POST'],
        format: 'json'
    )]
    public function updateFireworksApiKey(
        #[MapRequestPayload()] UpdateFireworksApiKeyDto $updateFireworksApiKeyDto,
        UpdateFireworksApiKeyService $updateFireworksApiKeyService
    ): JsonResponse {
        return $updateFireworksApiKeyService->updateApiKey($updateFireworksApiKeyDto);
    }

    #[Route(path: '/dashboard/change-password', name: 'app_dashboard_change_password', methods: ['POST'], format: 'json')]
    public function changePassword(
        #[MapRequestPayload()] UserPasswordChangeDto $userPasswordChangeDto,
        UserPasswordChangeService $userPasswordChangeService
    ): JsonResponse {
        return $userPasswordChangeService->changePassword($userPasswordChangeDto);
    }
}
