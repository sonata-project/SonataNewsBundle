<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Mailer;

use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Util\HashGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class Mailer implements MailerInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var array
     */
    protected $emails;

    /**
     * @var HashGeneratorInterface
     */
    protected $hashGenerator;

    /**
     * NEXT_MAJOR: Remove the support for `\Swift_Mailer` in this property.
     *
     * @var SymfonyMailerInterface|\Swift_Mailer
     */
    protected $mailer;

    /**
     * @var BlogInterface
     */
    protected $blog;

    public function __construct(object $mailer, BlogInterface $blog, HashGeneratorInterface $generator, RouterInterface $router, EngineInterface $templating, array $emails)
    {
        // NEXT_MAJOR: Remove the following 2 conditions and use `Symfony\Component\Mailer\MailerInterface` as argument declaration for `$mailer`.
        if (!$mailer instanceof SymfonyMailerInterface && !$mailer instanceof \Swift_Mailer) {
            throw new \TypeError(sprintf(
                'Argument 1 passed to "%s()" must be an instance of "%s" or "%s", instance of "%s" given.',
                __METHOD__,
                SymfonyMailerInterface::class,
                \Swift_Mailer::class,
                \get_class($mailer)
            ));
        }

        if (!$mailer instanceof SymfonyMailerInterface) {
            @trigger_error(sprintf(
                'Passing other type than "%s" as argument 1 for "%s()" is deprecated since sonata-project/user-bundle 4.x'
                .' and will be not supported in version 5.x.',
                SymfonyMailerInterface::class,
                __METHOD__
            ), \E_USER_DEPRECATED);
        }

        $this->blog = $blog;
        $this->mailer = $mailer;
        $this->hashGenerator = $generator;
        $this->router = $router;
        $this->templating = $templating;
        $this->emails = $emails;
    }

    public function sendCommentNotification(CommentInterface $comment): void
    {
        $rendered = $this->templating->render($this->emails['notification']['template'], [
            'comment' => $comment,
            'post' => $comment->getPost(),
            'hash' => $this->hashGenerator->generate($comment),
            'blog' => $this->blog,
        ]);

        $this->sendEmailMessage(
            $rendered,
            $this->emails['notification']['from'],
            $this->emails['notification']['emails']
        );
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        [$subject, $body] = explode("\n", trim($renderedTemplate), 2);

        // NEXT_MAJOR: Remove this condition.
        if ($this->mailer instanceof \Swift_Mailer) {
            $message = $this->mailer->createMessage()
                ->setSubject($subject)
                ->setFrom($fromEmail)
                ->setTo($toEmail)
                ->setBody($body);

            $this->mailer->send($message);

            return;
        }

        $email = (new Email())
                ->from($fromEmail)
                ->subject($subject)
                ->html($body);

        foreach ($this->emails['notification']['emails'] as $address) {
            $email->addTo($address);
        }

        $this->mailer->send($email);
    }
}
