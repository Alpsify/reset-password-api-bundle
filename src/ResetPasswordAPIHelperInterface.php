<?php

namespace Alpsify\ResetPasswordAPIBundle;
use Alpsify\ResetPasswordAPIBundle\Model\ResetPasswordRequestInterface;
use Alpsify\ResetPasswordAPIBundle\Model\Token;

/**
 * @author Nathan De Pachtere
 */
interface ResetPasswordAPIHelperInterface
{
    public function generateToken(object $user, $expiresAt): Token;

    public function persistTokenOnStorage(object $user, $expiresAt, Token $token): ResetPasswordRequestInterface;

    public function validateAndFetchToken(string $token): ResetPasswordRequestInterface;

    public function fetchUser(ResetPasswordRequestInterface $resetPasswordRequest, string $token): object;

    public function deleteResetPasswordRequest(string $token): void;

    public function getTokenLifeTime(): int;

    public function retrieveUserClass(string $type);

    public function saveNewPassword(object $user, string $plainPassword): void;
}
