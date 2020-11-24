<?php


namespace Alpsify\ResetPasswordAPIBundle\Util;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Nathan De Pachtere
 */
class ResetPasswordMailer
{

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    private string $template;
    private string $fromEmail;
    private string $fromName;

    public function __construct(MailerInterface $mailer, string $fromEmail, string $fromName, string $template)
    {
        $this->mailer = $mailer;
        $this->template = $template;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function sendResetLinkToUserEmail(string $token, int $tokenLifeTime, UserInterface $user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate($this->template)
            ->context([
                'token' => $token,
                'tokenLifetime' => $tokenLifeTime,
            ])
        ;

        $this->mailer->send($email);
    }
}
