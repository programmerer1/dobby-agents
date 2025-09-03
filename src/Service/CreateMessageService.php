<?php

namespace App\Service;

use App\Dto\CreateMessageDto;
use App\Entity\{Agent, Chat, Message};
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateMessageService
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly Message $message,
        public readonly MessageRepository $messageRepository,
        public readonly FireworksApiService $fireworksApiService,
        public readonly MessagePointHistoryService $messagePointHistoryService,
        public readonly UserService $userService
    ) {}

    public function createMessage(CreateMessageDto $createMessageDto, Agent $agent, Chat $chat): JsonResponse
    {
        $chatHistory = [];
        $messages = $this->messageRepository->findLastMessagesByChat($chat);

        $chatHistory = array_map(function ($message) {
            return [
                "role" => $message->getSender(),
                "content" => $message->getText()
            ];
        }, array_reverse($messages)); /* reverse, потому что доставали DESC */

        $response = $this->fireworksApiService->send($agent, $createMessageDto->text, $chatHistory);

        if (empty($response)) {
            return new JsonResponse(['status' => 500, 'error' => 'We couldn\'t get a response from the model.']);
        }

        $response = trim($response, '"');

        $this->message->setText($createMessageDto->text)
            ->setSender('user')
            ->setChat($chat);
        $this->entityManager->persist($this->message);

        $agentMessage = new Message;
        $agentMessage->setText($response)
            ->setSender('assistant')
            ->setChat($chat);
        $this->entityManager->persist($agentMessage);

        $this->messagePointHistoryService->setPoints($this->userService->getUser(), $this->message, $agent);

        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 200,
            'assistant' => [
                'role' => 'assistant',
                'content' => $response,
                'createdAt' => $agentMessage->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
