<?php


namespace Alpsify\ResetPasswordAPIBundle\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * @author Nathan De Pachtere
 */
trait ResetPasswordRequestTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $hashedToken;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $requestedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $expiresAt;

    /**
     * @ORM\Column(type="string", length=60)
     */
    protected $selector;

    /**
     * @return mixed
     */
    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    /**
     * @return mixed
     */
    public function getRequestedAt(): \DateTimeInterface
    {
        return $this->requestedAt;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * @return mixed
     */
    public function getSelector(): string
    {
        return $this->selector;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= \time();
    }
}
