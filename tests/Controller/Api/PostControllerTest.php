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

namespace Sonata\NewsBundle\Tests\Controller\Api;

use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Formatter\FormatterInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\NewsBundle\Controller\Api\PostController;
use Sonata\NewsBundle\Model\CommentInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PostControllerTest extends TestCase
{
    public function testGetPostsAction(): void
    {
        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $pager = $this->createMock('Sonata\DatagridBundle\Pager\PagerInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createPostController($postManager)->getPostsAction($paramFetcher));
    }

    public function testGetPostAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $this->assertSame($post, $this->createPostController($postManager)->getPostAction(1));
    }

    public function testGetPostNotFoundExceptionAction(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Post (42) not found');

        $this->createPostController()->getPostAction(42);
    }

    public function testGetPostCommentsAction(): void
    {
        $parameters = [
            'page' => 2,
            'count' => 5,
        ];

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));
        $paramFetcher->expects($this->exactly(2))->method('get')
            ->with($this->logicalOr($this->equalTo('page'), $this->equalTo('count')))
            ->will($this->returnCallback(function ($parameter) use ($parameters) {
                return $parameters[$parameter];
            }));

        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $pager = $this->createMock('Sonata\DatagridBundle\Pager\PagerInterface');

        // Will assert that param fetcher parameters are used for the pager
        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())
            ->method('getPager')
            ->with($this->anything(), $this->equalTo($parameters['page']), $this->equalTo($parameters['count']))
            ->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createPostController($postManager, $commentManager)->getPostCommentsAction(1, $paramFetcher));
    }

    public function testGetPostCommentsActionNotFoundExceptionAction(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Post (42) not found');

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');

        $this->createPostController()->getPostCommentsAction(42, $paramFetcher);
    }

    public function testPostPostAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('setContent');
        $post->expects($this->once())->method('getContentFormatter')->willReturn('text');
        $post->expects($this->once())->method('getRawContent')->willReturn('');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('save')->will($this->returnValue($post));

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects($this->once())->method('transform')->will($this->returnValue(''));
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($post));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->postPostAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostPostInvalidAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->never())->method('setContent');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->never())->method('save')->will($this->returnValue($post));

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects($this->never())->method('transform');
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->postPostAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutPostAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('setContent');
        $post->expects($this->once())->method('getContentFormatter')->willReturn('text');
        $post->expects($this->once())->method('getRawContent')->willReturn('');
        $post->expects($this->once())->method('getContent')->willReturn('');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));
        $postManager->expects($this->once())->method('save')->will($this->returnValue($post));

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects($this->once())->method('transform')->will($this->returnValue($post->getContent()));
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($post));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->putPostAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->never())->method('setContent');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));
        $postManager->expects($this->never())->method('save')->will($this->returnValue($post));

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects($this->never())->method('transform');
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->putPostAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeletePostAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));
        $postManager->expects($this->once())->method('delete');

        $view = $this->createPostController($postManager)->deletePostAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeletePostInvalidAction(): void
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $postManager->expects($this->never())->method('delete');

        $this->createPostController($postManager)->deletePostAction(1);
    }

    public function testPostPostCommentsAction(): void
    {
        $comment = $this->createMock(CommentInterface::class);
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('save');
        $commentManager->expects($this->once())->method('create')->will($this->returnValue($comment));

        $mailer = $this->createMock('Sonata\NewsBundle\Mailer\MailerInterface');
        $mailer->expects($this->once())->method('sendCommentNotification');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($comment));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $postController = $this->createPostController($postManager, $commentManager, $mailer, $formFactory);

        $postController->postPostCommentsAction(1, new Request());

        $this->assertInstanceOf(CommentInterface::class, $comment);
    }

    public function testPostPostCommentsInvalidFormAction(): void
    {
        $comment = $this->createMock(CommentInterface::class);
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('create')->will($this->returnValue($comment));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $this->assertInstanceOf('Symfony\Component\Form\Form', $this->createPostController($postManager, $commentManager, null, $formFactory)->postPostCommentsAction(1, new Request()));
    }

    public function testPostPostCommentsNotCommentableAction(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Post (42) not commentable');

        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(false));

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $this->createPostController($postManager)->postPostCommentsAction(42, new Request());
    }

    public function testPutPostCommentAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $comment = $this->createMock(CommentInterface::class);

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->once())->method('save');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('getData')->will($this->returnValue($comment));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $postController = $this->createPostController($postManager, $commentManager, null, $formFactory);

        $comment = $postController->putPostCommentsAction(1, 1, new Request());

        $this->assertInstanceOf(CommentInterface::class, $comment);
    }

    public function testPutPostCommentInvalidAction(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $comment = $this->createMock(CommentInterface::class);

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->never())->method('save');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, $commentManager, null, $formFactory)->putPostCommentsAction(1, 1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    /**
     * @param null $postManager
     * @param null $commentManager
     * @param null $mailer
     * @param null $formFactory
     * @param null $formatterPool
     *
     * @return PostController
     */
    protected function createPostController($postManager = null, $commentManager = null, $mailer = null, $formFactory = null, $formatterPool = null)
    {
        if (null === $postManager) {
            $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        }
        if (null === $commentManager) {
            $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        }
        if (null === $mailer) {
            $mailer = $this->createMock('Sonata\NewsBundle\Mailer\MailerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        }
        if (null === $formatterPool) {
            $formatterPool = new Pool('text');
            $formatterPool->add('text', $this->createMock(FormatterInterface::class));
        }

        return new PostController($postManager, $commentManager, $mailer, $formFactory, $formatterPool);
    }
}
