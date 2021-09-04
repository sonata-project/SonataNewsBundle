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
    public function testGetPostsAction()
    {
        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        $pager = $this->createMock('Sonata\DatagridBundle\Pager\PagerInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('getPager')->willReturn($pager);

        static::assertSame($pager, $this->createPostController($postManager)->getPostsAction($paramFetcher));
    }

    public function testGetPostAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        static::assertSame($post, $this->createPostController($postManager)->getPostAction(1));
    }

    public function testGetPostNotFoundExceptionAction()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Post (42) not found');

        $this->createPostController()->getPostAction(42);
    }

    public function testGetPostCommentsAction()
    {
        $parameters = [
            'page' => 2,
            'count' => 5,
        ];

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);
        $paramFetcher->expects(static::exactly(2))->method('get')
            ->with(static::logicalOr(static::equalTo('page'), static::equalTo('count')))
            ->willReturnCallback(static function ($parameter) use ($parameters) {
                return $parameters[$parameter];
            });

        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        $pager = $this->createMock('Sonata\DatagridBundle\Pager\PagerInterface');

        // Will assert that param fetcher parameters are used for the pager
        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())
            ->method('getPager')
            ->with(static::anything(), static::equalTo($parameters['page']), static::equalTo($parameters['count']))
            ->willReturn($pager);

        static::assertSame($pager, $this->createPostController($postManager, $commentManager)->getPostCommentsAction(1, $paramFetcher));
    }

    public function testGetPostCommentsActionNotFoundExceptionAction()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Post (42) not found');

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');

        $this->createPostController()->getPostCommentsAction(42, $paramFetcher);
    }

    public function testPostPostAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('setContent');
        $post->expects(static::once())->method('getContentFormatter')->willReturn('text');
        $post->expects(static::once())->method('getRawContent')->willReturn('');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('save')->willReturn($post);

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects(static::once())->method('transform')->willReturn('');
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($post);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->postPostAction(new Request());

        static::assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostPostInvalidAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::never())->method('setContent');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::never())->method('save')->willReturn($post);

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects(static::never())->method('transform');
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->postPostAction(new Request());

        static::assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutPostAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('setContent');
        $post->expects(static::once())->method('getContentFormatter')->willReturn('text');
        $post->expects(static::once())->method('getRawContent')->willReturn('');
        $post->expects(static::once())->method('getContent')->willReturn('');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);
        $postManager->expects(static::once())->method('save')->willReturn($post);

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects(static::once())->method('transform')->willReturn($post->getContent());
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($post);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->putPostAction(1, new Request());

        static::assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::never())->method('setContent');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);
        $postManager->expects(static::never())->method('save')->willReturn($post);

        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects(static::never())->method('transform');
        $formatterPool = new Pool('text');
        $formatterPool->add('text', $formatter);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->putPostAction(1, new Request());

        static::assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeletePostAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);
        $postManager->expects(static::once())->method('delete');

        $view = $this->createPostController($postManager)->deletePostAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeletePostInvalidAction()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn(null);
        $postManager->expects(static::never())->method('delete');

        $this->createPostController($postManager)->deletePostAction(1);
    }

    public function testPostPostCommentsAction()
    {
        $comment = $this->createMock(CommentInterface::class);
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('isCommentable')->willReturn(true);

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())->method('save');
        $commentManager->expects(static::once())->method('create')->willReturn($comment);

        $mailer = $this->createMock('Sonata\NewsBundle\Mailer\MailerInterface');
        $mailer->expects(static::once())->method('sendCommentNotification');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($comment);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $postController = $this->createPostController($postManager, $commentManager, $mailer, $formFactory);

        $postController->postPostCommentsAction(1, new Request());

        static::assertInstanceOf(CommentInterface::class, $comment);
    }

    public function testPostPostCommentsInvalidFormAction()
    {
        $comment = $this->createMock(CommentInterface::class);
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('isCommentable')->willReturn(true);

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())->method('create')->willReturn($comment);

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        static::assertInstanceOf('Symfony\Component\Form\Form', $this->createPostController($postManager, $commentManager, null, $formFactory)->postPostCommentsAction(1, new Request()));
    }

    public function testPostPostCommentsNotCommentableAction()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Post (42) not commentable');

        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('isCommentable')->willReturn(false);

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        $this->createPostController($postManager)->postPostCommentsAction(42, new Request());
    }

    public function testPutPostCommentAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('isCommentable')->willReturn(true);

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        $comment = $this->createMock(CommentInterface::class);

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())->method('find')->willReturn($comment);
        $commentManager->expects(static::once())->method('save');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('getData')->willReturn($comment);
        $form->expects(static::once())->method('isValid')->willReturn(true);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $postController = $this->createPostController($postManager, $commentManager, null, $formFactory);

        $comment = $postController->putPostCommentsAction(1, 1, new Request());

        static::assertInstanceOf(CommentInterface::class, $comment);
    }

    public function testPutPostCommentInvalidAction()
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects(static::once())->method('isCommentable')->willReturn(true);

        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects(static::once())->method('find')->willReturn($post);

        $comment = $this->createMock(CommentInterface::class);

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())->method('find')->willReturn($comment);
        $commentManager->expects(static::never())->method('save');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createPostController($postManager, $commentManager, null, $formFactory)->putPostCommentsAction(1, 1, new Request());

        static::assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
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
