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

use Sonata\NewsBundle\Entity\PostManager;

/**
 * Class PostManagerTest
 *
 * Tests the post manager entity.
 */
class PostManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function getPostManager($qbCallback)
    {
        $query = $this->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', array(), '', false, true, true, array('execute'));
        $query->expects($this->any())->method('execute')->will($this->returnValue(true));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $qb->expects($this->once())->method('orderBy')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('getQuery')->will($this->returnValue($query));

        $qbCallback($qb);

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($qb));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new PostManager('Sonata\NewsBundle\Entity\BasePost', $registry);
    }

    protected function assertRelationsEnabled($qb)
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

    protected function assertRelationsJoined($qb)
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

    protected function assertPostEnabled($qb, $flag)
    {
        $qb->expects($this->once())->method('andWhere')->with($this->equalTo('p.enabled = :enabled'));
        $qb->expects($this->once())->method('setParameters')->with($this->equalTo(array( 'enabled' => $flag )));
    }

    public function testGetPagerWithoutMode()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsEnabled($qb);
                $this->assertPostEnabled($qb, 1);
            })
            ->getPager(array(), 1);
    }

    public function testGetPagerWithoutModeEnabled()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsEnabled($qb);
                $this->assertPostEnabled($qb, 1);
            })
            ->getPager(array(), 1);
    }

    public function testGetPagerWithoutModeDisabled()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsEnabled($qb);
                $this->assertPostEnabled($qb, 0);
            })
            ->getPager(array(
                'enabled' => 0,
            ), 1);
    }

    public function testGetPagerWithPublicMode()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsEnabled($qb);
                $this->assertPostEnabled($qb, 1);
            })
            ->getPager(array(
                'mode' => 'public',
            ), 1);
    }

    public function testGetPagerWithPublicModeEnabled()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsEnabled($qb);
                $this->assertPostEnabled($qb, 1);
            })
            ->getPager(array(
                'mode'    => 'public',
                'enabled' => 1,
            ), 1);
    }

    public function testGetPagerWithPublicModeDisabled()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsEnabled($qb);
                $this->assertPostEnabled($qb, 0);
            })
            ->getPager(array(
                'mode'    => 'public',
                'enabled' => 0,
            ), 1);
    }

    public function testGetPagerWithAdminMode()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsJoined($qb);
                $qb->expects($this->never())->method('andWhere');
                $qb->expects($this->once())->method('setParameters')->with($this->equalTo(array()));
            })
            ->getPager(array(
                'mode' => 'admin',
            ), 1);
    }

    public function testGetPagerWithAdminModeEnabled()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsJoined($qb);
                $this->assertPostEnabled($qb, 1);
            })
            ->getPager(array(
                'mode'    => 'admin',
                'enabled' => 1,
            ), 1);
    }

    public function testGetPagerWithAdminModeDisabled()
    {
        $this
            ->getPostManager(function ($qb) {
                $this->assertRelationsJoined($qb);
                $this->assertPostEnabled($qb, 0);
            })
            ->getPager(array(
                'mode'    => 'admin',
                'enabled' => 0,
            ), 1);
    }
}