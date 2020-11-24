<?php
/**
 * @author Nathan De Pachtere
 */

namespace Alpsify\ResetPasswordAPIBundle\Model;


interface ResetPasswordRequestInterface
{
    public function initialize(\DateTimeInterface $expiresAt, string $selector, string $hashedToken): void;

    public function registerUser(object $user): void;

    public function fetchUser(): object;

    public function isExpired(): bool;

    public function getExpiresAt(): \DateTimeInterface;

    public function getHashedToken(): string;

    public function getRequestedAt(): \DateTimeInterface;
}
