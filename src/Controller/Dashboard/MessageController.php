<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Dto\CreateMessageDto;
use App\Entity\{Chat, User};
use App\Service\{CreateMessageService, PaginateMessageService};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

final class MessageController extends AbstractController
{
    #[Route('/dashboard/chat/{id}', name: 'app_dashboard_agent_chat_messages', methods: ['GET'])]
    public function index(Chat $chat): Response
    {
        $agent = $chat->getAgent();

        if ($this->isGranted('agent_chat', $agent) == false) {
            return $this->redirectToRoute('app_dashboard');
        }

        if ($this->isGranted('chat_view', $chat) == false) {
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/chat.html.twig', [
            'agent' => $chat->getAgent(),
            'current_chat' => $chat,
            'chats' =>
            $this->getUser()->getChats(),
        ]);
    }

    #[Route('/dashboard/chat/{id}', name: 'app_dashboard_agent_chat_message_new', methods: ['POST'], format: 'json')]
    public function createMessage(
        #[MapRequestPayload()] CreateMessageDto $createMessageDto,
        Chat $chat,
        CreateMessageService $createMessageService
    ): JsonResponse {
        $agent = $chat->getAgent();

        if ($this->isGranted('agent_chat', $agent) == false) {
            return $this->json(['status' => 403, 'error' => 'Access denied']);
        }

        if ($this->isGranted('chat_message_send', $chat) == false) {
            return $this->json(['status' => 403, 'error' => 'Access denied']);
        }

        return $createMessageService->createMessage($createMessageDto, $agent, $chat);
    }

    #[Route('/dashboard/chat/{id}/messages', name: 'app_dashboard_get_agent_chat_messages', methods: ['GET'], format: 'json')]
    public function getMessages(Chat $chat, PaginateMessageService $paginateMessageService): JsonResponse
    {
        $agent = $chat->getAgent();

        if ($this->isGranted('agent_chat', $agent) == false) {
            return $this->json(['status' => 403, 'error' => 'Access denied']);
        }

        if ($this->isGranted('chat_view', $chat) == false) {
            return $this->json(['status' => 403, 'error' => 'Access denied']);
        }

        return $paginateMessageService->getMessages($chat);
    }

    /** The VSC IDE did not recognize the methods, so I had to create this method.  */
    protected function getUser(): ?User
    {
        return parent::getUser();
    }
}
