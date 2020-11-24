<?php

namespace Alpsify\ResetPasswordAPIBundle\Exception;

final class TooManyResetPasswordRequestsException extends \Exception
{
    private $availableAt;

    public function __construct(\DateTimeInterface $availableAt, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->availableAt = $availableAt;
    }

    public function getAvailableAt(): \DateTimeInterface
    {
        return $this->availableAt;
    }

    public function getRetryAfter(): int
    {
        return $this->getAvailableAt()->getTimestamp() - (new \DateTime('now'))->getTimestamp();
    }

    public function getReason(): string
    {
        return 'You have already requested a reset password email. Please check your email or try again soon.';
    }
}
