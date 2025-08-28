<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findLastMessagesByChat(Chat $chat, int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.chat = :chat')
            ->setParameter('chat', $chat)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMessagesPaginated(Chat $chat, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.chat = :chat')
            ->setParameter('chat', $chat)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('m.id', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
