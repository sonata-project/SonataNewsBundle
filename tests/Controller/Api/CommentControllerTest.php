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
 * NEXT_MAJOR: Remove this class.
 *
 * @author Hugo Briand <briand@ekino.com>
 *
 * @group legacy
 */
class CommentControllerTest extends TestCase
{
    public function testGetCommentAction()
    {
        $comment = $this->createMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())->method('find')->willReturn($comment);

        static::assertSame($comment, $this->createCommentController($commentManager)->getCommentAction(1));
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
        $commentManager->expects(static::once())->method('find')->willReturn($comment);
        $commentManager->expects(static::once())->method('delete');

        $view = $this->createCommentController($commentManager)->deleteCommentAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeletePostInvalidAction()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects(static::once())->method('find')->willReturn(null);
        $commentManager->expects(static::never())->method('delete');

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
