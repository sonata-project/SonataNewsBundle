<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Tests\Entity;

use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\NewsBundle\Entity\PostManager;

/**
 * Class PostManagerTest.
 *
 * Tests the post manager entity.
 */
class PostManagerTest extends \PHPUnit_Framework_TestCase
{
    public function assertRelationsEnabled($qb)
    {
        $qb
            ->expects($this->exactly(2))
            ->method('leftJoin')
            ->with(
                $this->logicalOr(
                    $this->equalTo('p.tags'),
                    $this->equalTo('p.author')
                ),
                $this->logicalOr(
                    $this->equalTo('t'),
                    $this->equalTo('a')
                ),
                'WITH',
                $this->stringEndsWith('.enabled = true')
            )
            ->will($this->returnValue($qb))
        ;
    }

    public function assertRelationsJoined($qb)
    {
        $qb
            ->expects($this->exactly(2))
            ->method('leftJoin')
            ->with(
                $this->logicalOr(
                    $this->equalTo('p.tags'),
                    $this->equalTo('p.author')
                ),
                $this->logicalOr(
                    $this->equalTo('t'),
                    $this->equalTo('a')
                ),
                $this->isNull(),
                $this->isNull()
            )
            ->will($this->returnValue($qb))
        ;
    }

    public function assertPostEnabled($qb, $flag)
    {
        $qb->expects($this->once())->method('andWhere')->with($this->equalTo('p.enabled = :enabled'));
        $qb->expects($this->once())->method('setParameters')->with($this->equalTo(array('enabled' => $flag)));
    }

    public function testGetPagerWithoutMode()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager(array(), 1);
    }

    public function testGetPagerWithoutModeEnabled()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager(array(), 1);
    }

    public function testGetPagerWithoutModeDisabled()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager(array(
                'enabled' => 0,
            ), 1);
    }

    public function testGetPagerWithPublicMode()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager(array(
                'mode' => 'public',
            ), 1);
    }

    public function testGetPagerWithPublicModeEnabled()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager(array(
                'mode' => 'public',
                'enabled' => 1,
            ), 1);
    }

    public function testGetPagerWithPublicModeDisabled()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager(array(
                'mode' => 'public',
                'enabled' => 0,
            ), 1);
    }

    public function testGetPagerWithAdminMode()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsJoined($qb);
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(
                'mode' => 'admin',
            ), 1);
    }

    public function testGetPagerWithAdminModeEnabled()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager(array(
                'mode' => 'admin',
                'enabled' => 1,
            ), 1);
    }

    public function testGetPagerWithAdminModeDisabled()
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self) {
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager(array(
                'mode' => 'admin',
                'enabled' => 0,
            ), 1);
    }

    public function testGetPublicationDateQueryParts()
    {
        $result = $this
            ->getPostManager(function () {
            })
            ->getPublicationDateQueryParts('2010-02-10', 'month', 'n');

        $this->assertNotNull($result);
        $this->assertEquals(new \DateTime('2010-02-10'), $result['params']['startDate']);
        $this->assertEquals(new \DateTime('2010-03-10'), $result['params']['endDate']);
    }

    protected function getPostManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, array());

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new PostManager('Sonata\NewsBundle\Entity\BasePost', $registry);
    }
}
