<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\NewsBundle\Tests\Controller\Api;

use Sonata\NewsBundle\Controller\Api\PostController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;


/**
 * Class PostControllerTest
 *
 * @package Sonata\NewsBundle\Tests\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class PostControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPostsAction()
    {
        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $pager = $this->getMock('Sonata\DatagridBundle\Pager\PagerInterface');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertSame($pager, $this->createPostController($postManager)->getPostsAction($paramFetcher));
    }

    public function testGetPostAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $this->assertEquals($post, $this->createPostController($postManager)->getPostAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Post (42) not found
     */
    public function testGetPostNotFoundExceptionAction()
    {
        $this->createPostController()->getPostAction(42);
    }

    public function testGetPostCommentsAction()
    {
        $parameters = array(
            'page'  => 2,
            'count' => 5,
        );

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));
        $paramFetcher->expects($this->exactly(2))->method('get')
            ->with($this->logicalOr($this->equalTo('page'), $this->equalTo('count')))
            ->will($this->returnCallback(function($parameter) use ($parameters) {
                return $parameters[$parameter];
            }));

        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $pager = $this->getMock('Sonata\DatagridBundle\Pager\PagerInterface');

        // Will assert that param fetcher parameters are used for the pager
        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())
            ->method('getPager')
            ->with($this->anything(), $this->equalTo($parameters['page']), $this->equalTo($parameters['count']))
            ->will($this->returnValue($pager));

        $this->assertEquals($pager, $this->createPostController($postManager, $commentManager)->getPostCommentsAction(1, $paramFetcher));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Post (42) not found
     */
    public function testGetPostCommentsActionNotFoundExceptionAction()
    {
        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');

        $this->createPostController()->getPostCommentsAction(42, $paramFetcher);
    }

    public function testPostPostAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('setContent');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('save')->will($this->returnValue($post));

        $formatterPool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->once())->method('transform')->will($this->returnValue($post->getContent()));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($post));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->postPostAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostPostInvalidAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->never())->method('setContent');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->never())->method('save')->will($this->returnValue($post));

        $formatterPool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->never())->method('transform')->will($this->returnValue($post->getContent()));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->postPostAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutPostAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('setContent');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));
        $postManager->expects($this->once())->method('save')->will($this->returnValue($post));

        $formatterPool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->once())->method('transform')->will($this->returnValue($post->getContent()));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($post));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->putPostAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostInvalidAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->never())->method('setContent');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));
        $postManager->expects($this->never())->method('save')->will($this->returnValue($post));

        $formatterPool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->never())->method('transform')->will($this->returnValue($post->getContent()));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, null, null, $formFactory, $formatterPool)->putPostAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeletePostAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));
        $postManager->expects($this->once())->method('delete');

        $view = $this->createPostController($postManager)->deletePostAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeletePostInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $postManager->expects($this->never())->method('delete');

        $this->createPostController($postManager)->deletePostAction(1);
    }

    public function testPostPostCommentsAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('save');
        $commentManager->expects($this->once())->method('create')->will($this->returnValue($comment));

        $mailer = $this->getMock('Sonata\NewsBundle\Mailer\MailerInterface');
        $mailer->expects($this->once())->method('sendCommentNotification');

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($comment));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $this->assertInstanceOf('FOS\RestBundle\View\View', $this->createPostController($postManager, $commentManager, $mailer, $formFactory)->postPostCommentsAction(1, new Request()));
    }

    public function testPostPostCommentsInvalidFormAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('create')->will($this->returnValue($comment));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $this->assertInstanceOf('Symfony\Component\Form\Form', $this->createPostController($postManager, $commentManager, null, $formFactory)->postPostCommentsAction(1, new Request()));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Post (42) not commentable
     */
    public function testPostPostCommentsNotCommentableAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(false));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $this->createPostController($postManager)->postPostCommentsAction(42, new Request());
    }

    public function testPutPostCommentAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->once())->method('save')->will($this->returnValue($comment));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createPostController($postManager, $commentManager, null, $formFactory)->putPostCommentsAction(1, 1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutPostCommentInvalidAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('find')->will($this->returnValue($post));

        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->never())->method('save')->will($this->returnValue($comment));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
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
            $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        }
        if (null === $commentManager) {
            $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        }
        if (null === $mailer) {
            $mailer = $this->getMock('Sonata\NewsBundle\Mailer\MailerInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }
        if (null === $formatterPool) {
            $formatterPool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        }

        return new PostController($postManager, $commentManager, $mailer, $formFactory, $formatterPool);
    }
}
