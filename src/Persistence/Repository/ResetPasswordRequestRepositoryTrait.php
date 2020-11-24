<?php


namespace Alpsify\ResetPasswordAPIBundle\Persistence\Repository;


use Alpsify\ResetPasswordAPIBundle\Model\ResetPasswordRequestInterface;

/**
 * @author Nathan De Pachtere
 */
trait ResetPasswordRequestRepositoryTrait
{
    public function getUserIdentifier(object $user): string
    {
        return $this->getEntityManager()
            ->getUnitOfWork()
            ->getSingleIdentifierValue($user)
            ;
    }

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        // Normally there is only 1 max request per use, but written to be flexible
        $userReflectionClass = new \ReflectionClass(get_class($user));
        $propertyName = strtolower($userReflectionClass->getShortName());
        /** @var ResetPasswordRequestInterface $resetPasswordRequest */
        $resetPasswordRequest = $this->createQueryBuilder('t')
            ->where('t.'.$propertyName.' = :user')
            ->setParameter('user', $user)
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneorNullResult()
        ;

        if (null !== $resetPasswordRequest && !$resetPasswordRequest->isExpired()) {
            return $resetPasswordRequest->getRequestedAt();
        }

        return null;
    }

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {

        $userReflectionClass = new \ReflectionClass(get_class($resetPasswordRequest->fetchUser()));
        $propertyName = strtolower($userReflectionClass->getShortName());

        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.'.$propertyName.' = :user')
            ->setParameter('user', $resetPasswordRequest->fetchUser())
            ->getQuery()
            ->execute()
        ;
    }

    public function removeExpiredResetPasswordRequests(): int
    {
        // TODO: Implement removeExpiredResetPasswordRequests() method.
    }
}
