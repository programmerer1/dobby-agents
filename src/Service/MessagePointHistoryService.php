<?php

namespace App\Service;

use App\Entity\{MessagePointHistory, User, Message, Agent};
use Doctrine\ORM\EntityManagerInterface;

class MessagePointHistoryService
{
    private int $points = 1;
    public function __construct(public readonly EntityManagerInterface $entityManager) {}

    public function setPoints(User $user, Message $message, Agent $agent)
    {
        if ($agent->getUser() !== $user) {
            $messagePoint = new MessagePointHistory;
            $messagePoint->setUser($agent->getUser());
            $messagePoint->setMessage($message);
            $messagePoint->setPoints($this->points);
            $this->entityManager->persist($messagePoint);

            $agent->getUser()->setPoints($agent->getUser()->getPoints() + $this->points);
        }
    }
}
