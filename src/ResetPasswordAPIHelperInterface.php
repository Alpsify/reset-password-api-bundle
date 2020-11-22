<?php

namespace Alpsify\ResetPasswordAPIBundle;
use Alpsify\ResetPasswordAPIBundle\Model\Token;

/**
 * @author Nathan De Pachtere
 */
interface ResetPasswordAPIHelperInterface
{
    public function generateToken(object $user): Token;

    public function persistTokenFromStorage(Token $token): void;

    public function validateToken(string $token): object;

    public function fetchUser(): object;

    public function removeTokenFromStorage(string $token): void;

    public function getTokenLifeTime(): int;
}
