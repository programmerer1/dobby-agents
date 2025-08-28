<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Chat;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\{RequestStack, JsonResponse};

class PaginateMessageService
{
    private int $limit = 20;
    private int $page;
    private $offset;
    private MessageRepository $messageRepository;

    public function __construct(MessageRepository $messageRepository, RequestStack $requestStack)
    {
        $this->messageRepository = $messageRepository;
        $this->page = max(1, (int)$requestStack->getCurrentRequest()->query->get('page', 1));
        $this->offset = ($this->page - 1) * $this->limit;
    }

    public function getMessages(Chat $chat): JsonResponse
    {
        return $this->buildResponse($this->messageRepository->getMessagesPaginated($chat, $this->limit + 1, $this->offset));
    }

    private function buildResponse(array $messages): JsonResponse
    {
        $hasMore = count($messages) > $this->limit;

        if ($hasMore) {
            $messages = array_slice($messages, 0, $this->limit);
        }

        $data = array_map(fn($message) => [
            'role' => $message->getSender(),
            'content' => $message->getText(),
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $messages);

        return new JsonResponse([
            'messages' => $data,
            'hasMore' => $hasMore
        ]);
    }
}
