<?php


namespace Alpsify\ResetPasswordAPIBundle\Model;


/**
 * @author Nathan De Pachtere
 */
class Token
{
    /**
     * @var string
     */
    private $selector;

    /**
     * @var string
     */
    private $verifier;

    /**
     * @var string
     */
    private $hashedToken;

    public function __construct(string $selector, string $verifier, string $hashedToken)
    {
        $this->selector = $selector;
        $this->verifier = $verifier;
        $this->hashedToken = $hashedToken;
    }

    /**
     * @return string
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /**
     * @return string
     */
    public function getVerifier(): string
    {
        return $this->verifier;
    }

    /**
     * @return string
     */
    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }
}
