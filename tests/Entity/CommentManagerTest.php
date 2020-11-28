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

namespace Sonata\NewsBundle\Tests\Entity;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;
use Sonata\NewsBundle\Entity\BasePost;
use Sonata\NewsBundle\Entity\CommentManager;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;

/**
 * Tests the comment manager entity.
 *
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class CommentManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getCommentManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->once())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with(['status' => CommentInterface::STATUS_VALID]);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithAdminMode(): void
    {
        $self = $this;
        $this
            ->getCommentManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with([]);
            })
            ->getPager([
                'mode' => 'admin',
            ], 1);
    }

    public function testGetPagerWithStatus(): void
    {
        $self = $this;
        $this
            ->getCommentManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->once())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with(['status' => CommentInterface::STATUS_INVALID]);
            })
            ->getPager([
                'status' => CommentInterface::STATUS_INVALID,
            ], 1);
    }

    public function testGetPagerWithPostId(): void
    {
        $self = $this;
        $this
            ->getCommentManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->exactly(2))->method('andWhere')->with($self->logicalOr('c.post = :postId', 'c.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with(['postId' => 50, 'status' => CommentInterface::STATUS_VALID]);
            })
            ->getPager([
                'postId' => 50,
            ], 1);
    }

    protected function getCommentManager($qbCallback)
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        $postManager = $this->createMock(PostManagerInterface::class);

        return new CommentManager(BasePost::class, $registry, $postManager);
    }
}
