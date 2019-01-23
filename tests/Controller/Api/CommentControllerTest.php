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
use Sonata\NewsBundle\Controller\Api\CommentController;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentControllerTest extends TestCase
{
    public function testGetCommentAction()
    {
        $comment = $this->createMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));

        $this->assertSame($comment, $this->createCommentController($commentManager)->getCommentAction(1));
    }

    public function testGetCommentNotFoundExceptionAction()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Comment (42) not found');

        $this->createCommentController()->getCommentAction(42);
    }

    public function testDeleteCommentAction()
    {
        $comment = $this->createMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue($comment));
        $commentManager->expects($this->once())->method('delete');

        $view = $this->createCommentController($commentManager)->deleteCommentAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeletePostInvalidAction()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->will($this->returnValue(null));
        $commentManager->expects($this->never())->method('delete');

        $this->createCommentController($commentManager)->deleteCommentAction(1);
    }

    /**
     * @param null $commentManager
     *
     * @return CommentController
     */
    protected function createCommentController($commentManager = null)
    {
        if (null === $commentManager) {
            $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        }

        return new CommentController($commentManager);
    }
}
