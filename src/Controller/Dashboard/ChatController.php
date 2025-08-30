<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Dto\CreateMessageDto;
use App\Repository\AgentRepository;
use App\Service\CreateChatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Entity\User;

final class ChatController extends AbstractController
{
    #[Route('/dashboard/agent/{slug}/chat/new', name: 'app_dashboard_agent_chat', methods: ['GET'])]
    public function index(string $slug, AgentRepository $agentRepository): Response
    {
        $agent = $agentRepository->findOneBy(['username' => $slug]);

        if (empty($agent)) {
            return $this->redirectToRoute('app_dashboard');
        }

        if ($this->isGranted('agent_chat', $agent) == false) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/create-chat.html.twig', [
            'agent' => $agent,
            'chats' =>
            $this->getUser()->getChats(),
        ]);
    }

    #[Route('/dashboard/agent/{slug}/chat/new', name: 'app_dashboard_agent_chat_create', methods: ['POST'], format: 'json')]
    public function createChat(
        #[MapRequestPayload()] CreateMessageDto $createMessageDto,
        string $slug,
        AgentRepository $agentRepository,
        CreateChatService $createChatService
    ): JsonResponse {
        $agent = $agentRepository->findOneBy(['username' => $slug]);

        if (empty($agent)) {
            return $this->json(['status' => 404, 'error' => 'Agent not found']);
        }

        if ($this->isGranted('agent_chat', $agent) == false) {
            return $this->json(['status' => 403, 'error' => 'Access denied']);
        }

        return $createChatService->createChat($createMessageDto, $agent);
    }

    /** The VSC IDE did not recognize the methods, so I had to create this method.  */
    protected function getUser(): ?User
    {
        return parent::getUser();
    }
}
