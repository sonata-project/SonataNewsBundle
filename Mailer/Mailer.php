<?php

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
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var BlogInterface
     */
    protected $blog;

    /**
     * @param \Swift_Mailer          $mailer
     * @param HashGeneratorInterface $generator
     * @param RouterInterface        $router
     * @param EngineInterface        $templating
     * @param array                  $emails
     */
    public function __construct($mailer, BlogInterface $blog, HashGeneratorInterface $generator, RouterInterface $router, EngineInterface $templating, array $emails)
    {
        $this->blog = $blog;
        $this->mailer = $mailer;
        $this->hashGenerator = $generator;
        $this->router = $router;
        $this->templating = $templating;
        $this->emails = $emails;
    }

    /**
     * {@inheritdoc}
     */
    public function sendCommentNotification(CommentInterface $comment)
    {
        $rendered = $this->templating->render($this->emails['notification']['template'], array(
            'comment' => $comment,
            'post' => $comment->getPost(),
            'hash' => $this->hashGenerator->generate($comment),
            'blog' => $this->blog,
        ));

        $this->sendEmailMessage($rendered, $this->emails['notification']['from'], $this->emails['notification']['emails']);
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
