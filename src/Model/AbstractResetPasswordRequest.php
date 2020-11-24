<?php


namespace Alpsify\ResetPasswordAPIBundle\Model;

/**
 * @author Nathan De Pachtere
 */
abstract class AbstractResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    public function __construct(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->registerUser($user);
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function initialize(\DateTimeInterface $expiresAt, string $selector, string $hashedToken): void
    {
        $this->requestedAt = new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }
}
