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
    private int $selectorSize;

    public function __construct(string $signingKey, string $hashAlgo, RandomGenerator $randomGenerator, int $selectorSize)
    {
        $this->signingKey = $signingKey;
        $this->hashAlgo = $hashAlgo;
        $this->randomGenerator = $randomGenerator;
        $this->selectorSize = $selectorSize;
    }

    public function create(\DateTimeInterface $expiresAt, $userId, string $verifier = null): Token
    {
        if (null === $verifier) {
            $verifier = $this->randomGenerator->getRandomStr($this->selectorSize);
        }

        $selector = $this->randomGenerator->getRandomStr($this->selectorSize);

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

    public function getTokenSelectorSize(): int
    {
        return $this->selectorSize;
    }
}
