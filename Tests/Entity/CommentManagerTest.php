<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\NewsBundle\Tests\Entity;

use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\NewsBundle\Entity\CommentManager;
use Sonata\NewsBundle\Model\CommentInterface;


/**
 * Class CommentManagerTest
 *
 * Tests the comment manager entity.
 *
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class CommentManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function getCommentManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, array());

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $postManager = $this->getMock('Sonata\NewsBundle\Model\PostManagerInterface');

        return new CommentManager('Sonata\NewsBundle\Entity\BasePost', $registry, $postManager);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getCommentManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with(array('status' => CommentInterface::STATUS_VALID));
            })
            ->getPager(array(), 1);
    }

    public function testGetPagerWithAdminMode()
    {
        $self = $this;
        $this
            ->getCommentManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with(array());
            })
            ->getPager(array(
                'mode' => 'admin'
            ), 1);
    }

    public function testGetPagerWithStatus()
    {
        $self = $this;
        $this
            ->getCommentManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with(array('status' => CommentInterface::STATUS_INVALID));
            })
            ->getPager(array(
                'status' => CommentInterface::STATUS_INVALID
            ), 1);
    }

    public function testGetPagerWithPostId()
    {
        $self = $this;
        $this
            ->getCommentManager(function ($qb) use ($self) {
                $qb->expects($self->exactly(2))->method('andWhere')->with($self->logicalOr('c.post = :postId', 'c.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with(array('postId' => 50, 'status' => CommentInterface::STATUS_VALID));
            })
            ->getPager(array(
                'postId' => 50
            ), 1);
    }


}