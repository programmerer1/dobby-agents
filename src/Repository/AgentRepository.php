<?php

namespace App\Repository;

use App\Entity\Agent;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Agent>
 */
class AgentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agent::class);
    }

    public function userHasAccess(User $user, Agent $agent): bool
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->leftJoin('a.agentAccesses', 'acc')
            ->where('a = :agent')
            ->andWhere('a.isPublic = 1 OR a.user = :user OR acc.user = :user')
            ->setParameter('agent', $agent)
            ->setParameter('user', $user);

        return (int)$qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findUserAgentsPaginated(User $user, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('a.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function findVisibleForUserPaginated(User $user, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.agentAccesses', 'acc')
            ->addSelect('acc')
            ->where('a.isPublic = 1 OR a.user = :user OR acc.user = :user')
            ->setParameter('user', $user)
            ->setMaxResults($limit)
            // смещаем начало выборки (offset = (page-1)*limit)
            ->setFirstResult($offset)
            // сортируем по дате создания, новые сверху
            ->orderBy('a.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
