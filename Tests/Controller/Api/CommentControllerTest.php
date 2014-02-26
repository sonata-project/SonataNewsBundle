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

use Sonata\NewsBundle\Controller\Api\CommentController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class CommentControllerTest
 *
 * @package Sonata\NewsBundle\Tests\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentControllerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetCommentAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));

        $this->assertEquals($comment, $this->createCommentController($commentManager)->getCommentAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Comment (42) not found
     */
    public function testGetCommentNotFoundExceptionAction()
    {
        $this->createCommentController()->getCommentAction(42);
    }

    /**
     * @param null $commentManager
     * @param null $formFactory
     *
     * @return CommentController
     */
    protected function createCommentController($commentManager = null, $formFactory = null)
    {
        if (null === $commentManager) {
            $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        }

        if (null === $formFactory) {
            $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new CommentController($commentManager, $formFactory);
    }

    public function testPutCommentAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->once())->method('save')->will($this->returnValue($comment));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCommentController($commentManager, $formFactory)->putCommentAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutCommentInvalidAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->never())->method('save')->will($this->returnValue($comment));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCommentController($commentManager, $formFactory)->putCommentAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteCommentAction()
    {
        $comment = $this->getMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->once())->method('delete');

        $view = $this->createCommentController($commentManager)->deleteCommentAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeletePostInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $commentManager = $this->getMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $commentManager->expects($this->never())->method('delete');

        $this->createCommentController($commentManager)->deleteCommentAction(1);
    }
}
