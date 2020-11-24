<?php

namespace Alpsify\ResetPasswordAPIBundle\Exception;

final class InvalidResetPasswordRequestTokenException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getReason(): string
    {
        return 'Invalid reset token.';
    }
}
