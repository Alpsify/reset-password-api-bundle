<?php


namespace Alpsify\ResetPasswordAPIBundle\Persistence\Repository;

use Alpsify\ResetPasswordAPIBundle\Model\ResetPasswordRequestInterface;

/**
 * @author Nathan De Pachtere
 */
interface ResetPasswordRequestRepositoryInterface
{
    public function getUserIdentifier(object $user): string;

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface;

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface;

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void;

    public function removeExpiredResetPasswordRequests(): int;
}
