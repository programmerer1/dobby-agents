<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserPasswordResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use \DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<UserPasswordResetToken>
 */
class UserPasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPasswordResetToken::class);
    }

    public function findValidToken(string $token): ?UserPasswordResetToken
    {
        return $this->createQueryBuilder('t')
            ->where('t.token = :token')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('token', $token)
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteUserTokens(User $user): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
