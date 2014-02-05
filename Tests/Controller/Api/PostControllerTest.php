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

        $pager = $this->getMockBuilder('Sonata\AdminBundle\Datagrid\Pager')->disableOriginalConstructor()->getMock();
        $pager->expects($this->once())->method('getResults')->will($this->returnValue(array('returned')));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('getPager')->will($this->returnValue($pager));

        $this->assertEquals(array('returned'), $this->createPostController($postManager)->getPostsAction($paramFetcher));
    }

    public function testGetPostAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('findOneBy')->will($this->returnValue($post));

        $this->assertEquals($post, $this->createPostController($postManager)->getPostAction(1));
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Post (42) not found
     */
    public function testGetPostNotFoundExceptionAction()
    {
        $this->createPostController()->getPostAction(42);
    }

    public function testGetPostCommentsAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('getComments')->will($this->returnValue(array($comment)));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('findOneBy')->will($this->returnValue($post));

        $this->assertEquals(array($comment), $this->createPostController($postManager)->getPostCommentsAction(1));
    }

    public function testPostPostCommentsAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(true));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('findOneBy')->will($this->returnValue($post));

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
        $postManager->expects($this->once())->method('findOneBy')->will($this->returnValue($post));

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
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Post (42) not commentable
     */
    public function testPostPostCommentsNotCommentableAction()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->once())->method('isCommentable')->will($this->returnValue(false));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');
        $postManager->expects($this->once())->method('findOneBy')->will($this->returnValue($post));

        $this->createPostController($postManager)->postPostCommentsAction(42, new Request());
    }

    /**
     * @param null $postManager
     * @param null $commentManager
     * @param null $mailer
     * @param null $formFactory
     *
     * @return PostController
     */
    protected function createPostController($postManager = null, $commentManager = null, $mailer = null, $formFactory = null)
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

        return new PostController($postManager, $commentManager, $mailer, $formFactory);
    }
}
