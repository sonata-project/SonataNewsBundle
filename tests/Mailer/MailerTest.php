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

namespace Sonata\NewsBundle\Tests\Mailer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\NewsBundle\Mailer\Mailer;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Util\HashGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class MailerTest extends TestCase
{
    /**
     * @var RouterInterface|MockObject
     */
    private $router;

    /**
     * @var BlogInterface|MockObject
     */
    private $blog;

    /**
     * @var HashGeneratorInterface|MockObject
     */
    private $generator;

    /**
     * @var EngineInterface|MockObject
     */
    private $templating;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var array
     */
    private $emails;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(SymfonyMailerInterface::class);
        $this->blog = $this->createMock(BlogInterface::class);
        $this->generator = $this->createMock(HashGeneratorInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->templating = $this->createMock(EngineInterface::class);
        $this->emails = [
            'notification' => [
                'template' => 'email_test.html.twig',
                'from' => 'news-bundle@sonata-project.org',
                'emails' => ['news-bundle@sonata-project.org', 'news-bundle-anoher@sonata-project.org'],
            ],
        ];
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @group legacy
     *
     * @dataProvider emailTemplateData
     */
    public function testSendCommentNotificationWithSwiftMailer(string $template, string $subject, string $body): void
    {
        $post = $this->createMock(PostInterface::class);

        $comment = $this->createStub(CommentInterface::class);
        $comment
            ->expects($this->once())
            ->method('getPost')
            ->willReturn($post);

        $this->generator
            //->expects($this->once())
            ->method('generate')
            ->with($comment)
            ->willReturn('some_result');

        $this->emails['notification']['template'] = $template;

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with($this->emails['notification']['template'], [
                'comment' => $comment,
                'post' => $post,
                'hash' => 'some_result',
                'blog' => $this->blog,
            ])
            ->willReturn($template);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($this->emails['notification']['from'])
            ->setTo($this->emails['notification']['emails'])
            ->setBody($body);

        $this->mailer = $this->createStub(\Swift_Mailer::class);
        $this->mailer
            ->expects($this->once())
            ->method('createMessage')
            ->willReturn($message);

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($message);

        $mailer = $this->getMailer();

        $mailer->sendCommentNotification($comment);
    }

    /**
     * @dataProvider emailTemplateData
     */
    public function testSendCommentNotification(string $template, string $subject, string $body): void
    {
        $post = $this->createMock(PostInterface::class);

        $comment = $this->createStub(CommentInterface::class);
        $comment
            ->expects($this->once())
            ->method('getPost')
            ->willReturn($post);

        $this->generator
            //->expects($this->once())
            ->method('generate')
            ->with($comment)
            ->willReturn('some_result');

        $this->emails['notification']['template'] = $template;

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with($this->emails['notification']['template'], [
                'comment' => $comment,
                'post' => $post,
                'hash' => 'some_result',
                'blog' => $this->blog,
            ])
            ->willReturn($template);

        $email = (new Email())
            ->from($this->emails['notification']['from'])
            ->subject($subject)
            ->html($body);

        foreach ($this->emails['notification']['emails'] as $address) {
            $email->addTo($address);
        }

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($email);

        $mailer = $this->getMailer();

        $mailer->sendCommentNotification($comment);
    }

    public function emailTemplateData(): array
    {
        return [
            //'CR' => ["Subject\rFirst line\rSecond line", 'Subject', "First line\rSecond line"],
            'LF' => ["Subject\nFirst line\nSecond line", 'Subject', "First line\nSecond line"],
            //'CRLF' => ["Subject\r\nFirst line\r\nSecond line", 'Subject', "First line\r\nSecond line"],
            'LFLF' => ["Subject\n\nFirst line\n\nSecond line", 'Subject', "\nFirst line\n\nSecond line"],
            //'CRCR' => ["Subject\r\rFirst line\r\rSecond line", 'Subject', "\rFirst line\r\rSecond line"],
        ];
    }

    private function getMailer(): Mailer
    {
        return new Mailer($this->mailer, $this->blog, $this->generator, $this->router, $this->templating, $this->emails);
    }
}
