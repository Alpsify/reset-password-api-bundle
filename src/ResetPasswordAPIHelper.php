<?php

namespace Alpsify\ResetPasswordAPIBundle;
use Alpsify\ResetPasswordAPIBundle\Exception\TooManyResetPasswordRequestsException;
use Alpsify\ResetPasswordAPIBundle\Generator\TokenGenerator;
use Alpsify\ResetPasswordAPIBundle\Generator\TokenGeneratorInterface;
use Alpsify\ResetPasswordAPIBundle\Model\ResetPasswordRequestInterface;
use Alpsify\ResetPasswordAPIBundle\Model\Token;

/**
 * @author Nathan De Pachtere
 */
class ResetPasswordAPIHelper implements ResetPasswordAPIHelperInterface
{
    /**
     * @var TokenGenerator
     */
    private TokenGeneratorInterface $tokenGenerator;
    private int $tokenLifeTime;
    private int $throttleTime;

    public function __construct(TokenGeneratorInterface $tokenGenerator, int $tokenLifeTime, int $throttleTime)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenLifeTime = $tokenLifeTime;
        $this->throttleTime = $throttleTime;
    }

    public function getTokenLifetime(): int
    {
        return $this->tokenLifeTime;
    }

    public function generateToken(object $user): Token
    {
        if ($availableAt = $this->hasUserHitThrottling($user)) {
            throw new TooManyResetPasswordRequestsException($availableAt);
        }

        $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', $this->tokenLifeTime));

        $token = $this->tokenGenerator->create($expiresAt, $this->repository->getUserIdentifier($user));

        return $token;
    }

    public function persistTokenFromStorage(Token $token): void
    {
        // TODO: Implement persistTokenFromStorage() method.
    }

    public function validateToken(string $token): object
    {
        // TODO: Implement validateToken() method.
    }

    public function fetchUser(): object
    {
        // TODO: Implement fetchUser() method.
    }

    public function removeTokenFromStorage(string $token): void
    {
        // TODO: Implement removeTokenFromStorage() method.
    }

    private function findResetPasswordRequest(string $token): ?ResetPasswordRequestInterface
    {
        $selector = \substr($token, 0, self::SELECTOR_LENGTH);

        return $this->repository->findResetPasswordRequest($selector);
    }

    private function hasUserHitThrottling(object $user): ?\DateTimeInterface
    {
        /** @var \DateTime|\DateTimeImmutable|null $lastRequestDate */
        $lastRequestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        if (null === $lastRequestDate) {
            return null;
        }

        $availableAt = (clone $lastRequestDate)->add(new \DateInterval("PT{$this->throttleTime}S"));

        if ($availableAt > new \DateTime('now')) {
            return $availableAt;
        }

        return null;
    }
}
