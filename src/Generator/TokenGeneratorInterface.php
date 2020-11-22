<?php
/**
 * @author Nathan De Pachtere
 */

namespace Alpsify\ResetPasswordAPIBundle\Generator;


use Alpsify\ResetPasswordAPIBundle\Model\Token;

interface TokenGeneratorInterface
{
    public function create(\DateTimeInterface $expiresAt, $userId, string $verifier = null): Token;
}
