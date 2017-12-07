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

use PHPUnit\Framework\TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\NewsBundle\Entity\PostManager;

/**
 * Tests the post manager entity.
 */
class PostManagerTest extends TestCase
{
    public function assertRelationsEnabled($qb): void
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

    public function assertRelationsJoined($qb): void
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

    public function assertPostEnabled($qb, $flag): void
    {
        $qb->expects($this->once())->method('andWhere')->with($this->equalTo('p.enabled = :enabled'));
        $qb->expects($this->once())->method('setParameters')->with($this->equalTo(['enabled' => $flag]));
    }

    public function testFindOneByPermalinkSlug(): void
    {
        $permalink = $this->createMock('Sonata\NewsBundle\Permalink\PermalinkInterface');
        $permalink->expects($this->once())->method('getParameters')
            ->with($this->equalTo('foo/bar'))
            ->will($this->returnValue([
                'slug' => 'bar',
            ]));

        $blog = $this->createMock('Sonata\NewsBundle\Model\BlogInterface');
        $blog->expects($this->once())->method('getPermalinkGenerator')->will($this->returnValue($permalink));

        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('p.slug = :slug'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['slug' => 'bar']));
            })
            ->findOneByPermalink('foo/bar', $blog);
    }

    public function testFindOneByPermalinkException(): void
    {
        $permalink = $this->createMock('Sonata\NewsBundle\Permalink\PermalinkInterface');
        $permalink->expects($this->once())->method('getParameters')
            ->with($this->equalTo(''))
            ->willThrowException(new \InvalidArgumentException());

        $blog = $this->createMock('Sonata\NewsBundle\Model\BlogInterface');
        $blog->expects($this->once())->method('getPermalinkGenerator')->will($this->returnValue($permalink));

        $self = $this;
        $result = $this
            ->getPostManager(function ($qb): void {
            })
            ->findOneByPermalink('', $blog);

        $this->assertNull($result);
    }

    public function testGetPagerWithoutMode(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithoutModeEnabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithoutModeDisabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'enabled' => 0,
            ], 1);
    }

    public function testGetPagerWithPublicMode(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'public',
            ], 1);
    }

    public function testGetPagerWithPublicModeEnabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'public',
                'enabled' => 1,
            ], 1);
    }

    public function testGetPagerWithPublicModeDisabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'mode' => 'public',
                'enabled' => 0,
            ], 1);
    }

    public function testGetPagerWithAdminMode(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $self->assertRelationsJoined($qb);
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([
                'mode' => 'admin',
            ], 1);
    }

    public function testGetPagerWithAdminModeEnabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'admin',
                'enabled' => 1,
            ], 1);
    }

    public function testGetPagerWithAdminModeDisabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'mode' => 'admin',
                'enabled' => 0,
            ], 1);
    }

    public function testGetPublicationDateQueryParts(): void
    {
        $result = $this
            ->getPostManager(function (): void {
            })
            ->getPublicationDateQueryParts('2010-02-10', 'month', 'n');

        $this->assertNotNull($result);
        $this->assertEquals(new \DateTime('2010-02-10'), $result['params']['startDate']);
        $this->assertEquals(new \DateTime('2010-03-10'), $result['params']['endDate']);
    }

    protected function getPostManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, []);

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new PostManager('Sonata\NewsBundle\Entity\BasePost', $registry);
    }
}
