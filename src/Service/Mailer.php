<?php
namespace App\Service;

use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class Mailer
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(
        Environment $twig,
        \Swift_Mailer $mailer
    )
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render(
            'confirmation.html.twig',
            [
                'user' => $user
            ]
        );

        $message = (new Swift_Message('Please confirm your account!'))
            ->setFrom('meldev996@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
