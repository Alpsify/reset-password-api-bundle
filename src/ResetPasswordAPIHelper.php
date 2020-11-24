<?php

namespace Alpsify\ResetPasswordAPIBundle;
use Alpsify\ResetPasswordAPIBundle\Exception\ExpiredResetPasswordRequestTokenException;
use Alpsify\ResetPasswordAPIBundle\Exception\InvalidResetPasswordRequestTokenException;
use Alpsify\ResetPasswordAPIBundle\Exception\TooManyResetPasswordRequestsException;
use Alpsify\ResetPasswordAPIBundle\Generator\TokenGenerator;
use Alpsify\ResetPasswordAPIBundle\Generator\TokenGeneratorInterface;
use Alpsify\ResetPasswordAPIBundle\Model\ResetPasswordRequestInterface;
use Alpsify\ResetPasswordAPIBundle\Model\Token;
use Alpsify\ResetPasswordAPIBundle\Persistence\Repository\ResetPasswordRequestRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    private array $userTypes;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var string
     */
    private string $resetPasswordRequestClass;
    /**
     * @var ResetPasswordRequestRepositoryInterface
     */
    private ResetPasswordRequestRepositoryInterface $resetPasswordRequestRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(TokenGeneratorInterface $tokenGenerator, int $tokenLifeTime, int $throttleTime, array $userTypes, EntityManagerInterface $entityManager, ResetPasswordRequestRepositoryInterface $resetPasswordRequestRepository, string $resetPasswordRequestClass, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenLifeTime = $tokenLifeTime;
        $this->throttleTime = $throttleTime;
        $this->userTypes = $userTypes;
        $this->entityManager = $entityManager;
        $this->resetPasswordRequestClass = $resetPasswordRequestClass;
        $this->resetPasswordRequestRepository = $resetPasswordRequestRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getTokenLifetime(): int
    {
        return $this->tokenLifeTime;
    }

    public function generateToken(object $user, $expiresAt): Token
    {
        if ($availableAt = $this->hasUserHitThrottling($user)) {
            throw new TooManyResetPasswordRequestsException($availableAt);
        }

        $token = $this->tokenGenerator->create($expiresAt, $this->resetPasswordRequestRepository->getUserIdentifier($user));

        return $token;
    }

    public function persistTokenOnStorage(object $user, $expiresAt, Token $token): ResetPasswordRequestInterface
    {
        $class = $this->resetPasswordRequestClass;
        $resetPasswordRequest = new $class($user, $expiresAt, $token->getSelector(), $token->getHashedToken());

        $this->entityManager->persist($resetPasswordRequest);
        $this->entityManager->flush();

        return $resetPasswordRequest;
    }

    public function validateAndFetchToken(string $token): ResetPasswordRequestInterface
    {
        if(($this->tokenGenerator->getTokenSelectorSize()*2) !== \strlen($token)) {
            throw new InvalidResetPasswordRequestTokenException();
        }

        $resetPasswordRequest = $this->findResetPasswordRequest($token);

        if (null === $resetPasswordRequest) {
            throw new InvalidResetPasswordRequestTokenException();
        }

        if ($resetPasswordRequest->isExpired()) {
            throw new ExpiredResetPasswordRequestTokenException();
        }

        return $resetPasswordRequest;
    }

    public function fetchUser(ResetPasswordRequestInterface $resetPasswordRequest, string $token): object
    {
        $user = $resetPasswordRequest->fetchUser();

        dump(\substr($token, $this->tokenGenerator->getTokenSelectorSize()));

        $hashedVerifierToken = $this->tokenGenerator->create(
            $resetPasswordRequest->getExpiresAt(),
            $this->resetPasswordRequestRepository->getUserIdentifier($user),
            \substr($token, $this->tokenGenerator->getTokenSelectorSize())
        );

        if (false === \hash_equals($resetPasswordRequest->getHashedToken(), $hashedVerifierToken->getHashedToken())) {
            dump('yolo');
            throw new InvalidResetPasswordRequestTokenException();
        }

        return $user;
    }

    public function saveNewPassword(object $user, string $plainPassword): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword(
            $user,
            $plainPassword
        );
        $user->setPassword($encodedPassword);
        $this->entityManager->flush();
    }

    public function deleteResetPasswordRequest(string $token): void
    {
        $request = $this->findResetPasswordRequest($token);

        if (null === $request) {
            throw new InvalidResetPasswordRequestTokenException();
        }

        $this->resetPasswordRequestRepository->removeResetPasswordRequest($request);
    }

    private function findResetPasswordRequest(string $token): ?ResetPasswordRequestInterface
    {
        $selector = \substr($token, 0, $this->tokenGenerator->getTokenSelectorSize());

        return $this->resetPasswordRequestRepository->findResetPasswordRequest($selector);
    }

    private function hasUserHitThrottling(object $user): ?\DateTimeInterface
    {
        /** @var \DateTime|\DateTimeImmutable|null $lastRequestDate */
        $lastRequestDate = $this->resetPasswordRequestRepository->getMostRecentNonExpiredRequestDate($user);

        if (null === $lastRequestDate) {
            return null;
        }

        $availableAt = (clone $lastRequestDate)->add(new \DateInterval("PT{$this->throttleTime}S"));

        if ($availableAt > new \DateTime('now')) {
            return $availableAt;
        }

        return null;
    }

    public function retrieveUserClass(string $type)
    {
        $reference = new $this->userTypes[$type]['class'];
        return get_class($reference);
    }
}
