<?php


namespace Alpsify\ResetPasswordAPIBundle\Model;


/**
 * @author Nathan De Pachtere
 */
trait ResetPasswordRequestTrait
{
    private function initialize(\DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->requestedAt = new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }
}
