<?php


namespace Alpsify\ResetPasswordAPIBundle\Controller;

use Alpsify\ResetPasswordAPIBundle\Exception\TooManyResetPasswordRequestsException;
use Alpsify\ResetPasswordAPIBundle\ResetPasswordAPIHelperInterface;
use Alpsify\ResetPasswordAPIBundle\Util\ResetPasswordMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Nathan De Pachtere
 */
class ResetPasswordController extends AbstractController
{

    /**
     * @var ResetPasswordAPIHelperInterface
     */
    private ResetPasswordAPIHelperInterface $resetPasswordAPIHelper;
    /**
     * @var ResetPasswordMailer
     */
    private ResetPasswordMailer $resetPasswordMailer;

    public function __construct(ResetPasswordAPIHelperInterface $resetPasswordAPIHelper, ResetPasswordMailer $resetPasswordMailer)
    {

        $this->resetPasswordAPIHelper = $resetPasswordAPIHelper;
        $this->resetPasswordMailer = $resetPasswordMailer;
    }

    public function request(Request $request)
    {
        $data = \json_decode($request->getContent(), true);

        $user = $this->getDoctrine()->getRepository($this->resetPasswordAPIHelper->retrieveUserClass($data['type']))->findOneBy([
            'email' => $data['email'],
        ]);

        $expiresAt = new \DateTimeImmutable(\sprintf('+%d seconds', $this->resetPasswordAPIHelper->getTokenLifeTime()));

        try {
            $token = $this->resetPasswordAPIHelper->generateToken($user, $expiresAt);
        }catch (TooManyResetPasswordRequestsException $e) {
            return $this->json([ "reason" => $e->getReason(), "retryAfter" => $e->getRetryAfter()], 403);
        }

        $this->resetPasswordAPIHelper->persistTokenOnStorage($user, $expiresAt, $token);

        $this->resetPasswordMailer->sendResetLinkToUserEmail($token->getPublicToken(), $this->resetPasswordAPIHelper->getTokenLifetime(), $user);

        return $this->json(['lifetime' => $this->resetPasswordAPIHelper->getTokenLifetime()], 200);
    }

    public function reset(Request $request)
    {
        $data = \json_decode($request->getContent(), true);

        if(!isset($data['token']) || !isset($data['plainPassword']))
        {
            return $this->json(['NOK'], 400);
        }

        try {
            $resetPasswordRequest = $this->resetPasswordAPIHelper->validateAndFetchToken($data['token']);
            $user = $this->resetPasswordAPIHelper->fetchUser($resetPasswordRequest, $data['token']);
        } catch(\Exception $e) {
            return $this->json(['reason' => $e->getReason()], 400);
        }

        $this->resetPasswordAPIHelper->saveNewPassword($user, $data['plainPassword']);

        $this->resetPasswordAPIHelper->deleteResetPasswordRequest($data['token']);

        return $this->json([], 204);
    }
}
