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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentControllerTest extends TestCase
{
    public function testGetCommentAction()
    {
        $comment = $this->createMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->willReturn($comment);

        $this->assertSame($comment, $this->createCommentController($commentManager)->getCommentAction(1));
    }

    /**
     * @dataProvider getIdsForNotFound
     */
    public function testGetCommentNotFoundExceptionAction($identifier, string $message): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage($message);

        $this->createCommentController()->getCommentAction($identifier);
    }

    /**
     * @phpstan-return list<array{mixed, string}>
     */
    public function getIdsForNotFound(): array
    {
        return [
            [42, 'Comment not found for identifier 42.'],
            ['42', 'Comment not found for identifier \'42\'.'],
            [null, 'Comment not found for identifier NULL.'],
            ['', 'Comment not found for identifier \'\'.'],
        ];
    }

    public function testDeleteCommentAction()
    {
        $comment = $this->createMock('Sonata\NewsBundle\Model\CommentInterface');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->willReturn($comment);
        $commentManager->expects($this->once())->method('delete');

        $view = $this->createCommentController($commentManager)->deleteCommentAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeletePostInvalidAction()
    {
        $this->expectException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $commentManager = $this->createMock('Sonata\NewsBundle\Model\CommentManagerInterface');
        $commentManager->expects($this->once())->method('find')->willReturn(null);
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
