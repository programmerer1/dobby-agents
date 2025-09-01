<?php

namespace App\Service;

use App\Dto\CreateMessageDto;
use App\Entity\{Agent, Chat, Message};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreateChatService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly Chat $chat,
        public readonly Message $message,
        public readonly UrlGeneratorInterface $urlGeneratorInterface,
        public readonly FireworksApiService $fireworksApiService,
        public readonly UserService $userService,
    ) {}

    public function createChat(CreateMessageDto $createMessageDto, Agent $agent): JsonResponse
    {
        $this->chat->setTitle(mb_substr($createMessageDto->text, 0, 27, 'UTF-8'))
            ->setAgent($agent)->setUser($this->userService->getUser());

        $response = $this->fireworksApiService->send($agent, $createMessageDto->text);

        if (empty($response)) {
            return new JsonResponse(['status' => 500, 'error' => 'We couldn\'t get a response from the model.']);
        }

        $response = trim($response, '"');
        $this->entityManager->persist($this->chat);

        $this->message->setText($createMessageDto->text)
            ->setSender('user')
            ->setChat($this->chat);
        $this->entityManager->persist($this->message);

        $agentMessage = new Message;
        $agentMessage->setText($response)
            ->setSender('assistant')
            ->setChat($this->chat);
        $this->entityManager->persist($agentMessage);

        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 200,
            'redirect' => $this->urlGeneratorInterface->generate(
                'app_dashboard_agent_chat_messages',
                ['id' => $this->chat->getId()]
            )
        ]);
    }
}
