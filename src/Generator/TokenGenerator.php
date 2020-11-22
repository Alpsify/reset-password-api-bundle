<?php

namespace Alpsify\ResetPasswordAPIBundle\Generator;

use Alpsify\ResetPasswordAPIBundle\Model\Token;

class TokenGenerator implements TokenGeneratorInterface
{
    private $signingKey;

    private string $hashAlgo;
    /**
     * @var RandomGenerator
     */
    private RandomGenerator $randomGenerator;

    public function __construct(string $signingKey, string $hashAlgo, RandomGenerator $randomGenerator)
    {
        $this->signingKey = $signingKey;
        $this->hashAlgo = $hashAlgo;
        $this->randomGenerator = $randomGenerator;
    }

    public function create(\DateTimeInterface $expiresAt, $userId, string $verifier = null): Token
    {
        if (null === $verifier) {
            $verifier = $this->randomGenerator->getRandomStr();
        }

        $selector = $this->randomGenerator->getRandomStr();

        $encodedData = \json_encode([$verifier, $userId, $expiresAt->getTimestamp()]);

        return new Token(
            $selector,
            $verifier,
            $this->getHashedToken($encodedData)
        );
    }

    private function getHashedToken(string $data): string
    {
        return \base64_encode(\hash_hmac($this->hashAlgo, $data, $this->signingKey, true));
    }
}
