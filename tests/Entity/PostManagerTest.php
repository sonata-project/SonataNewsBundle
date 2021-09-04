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
use Sonata\NewsBundle\Entity\PostManager;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Permalink\PermalinkInterface;

class PostManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function assertRelationsEnabled($qb)
    {
        $qb
            ->expects(static::exactly(2))
            ->method('leftJoin')
            ->with(
                static::logicalOr(
                    static::equalTo('p.tags'),
                    static::equalTo('p.author')
                ),
                static::logicalOr(
                    static::equalTo('t'),
                    static::equalTo('a')
                ),
                'WITH',
                static::stringEndsWith('.enabled = true')
            )
            ->willReturn($qb);
    }

    public function assertRelationsJoined($qb)
    {
        $qb
            ->expects(static::exactly(2))
            ->method('leftJoin')
            ->with(
                static::logicalOr(
                    static::equalTo('p.tags'),
                    static::equalTo('p.author')
                ),
                static::logicalOr(
                    static::equalTo('t'),
                    static::equalTo('a')
                ),
                static::isNull(),
                static::isNull()
            )
            ->willReturn($qb);
    }

    public function assertPostEnabled($qb, $flag)
    {
        $qb->expects(static::once())->method('andWhere')->with(static::equalTo('p.enabled = :enabled'));
        $qb->expects(static::once())->method('setParameters')->with(static::equalTo(['enabled' => $flag]));
    }

    public function testFindOneByPermalinkSlug()
    {
        $permalink = $this->createMock(PermalinkInterface::class);
        $permalink->expects(static::once())->method('getParameters')
            ->with(static::equalTo('foo/bar'))
            ->willReturn([
                'slug' => 'bar',
            ]);

        $blog = $this->createMock(BlogInterface::class);
        $blog->expects(static::once())->method('getPermalinkGenerator')->willReturn($permalink);

        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('p.slug = :slug'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['slug' => 'bar']));
            })
            ->findOneByPermalink('foo/bar', $blog);
    }

    public function testFindOneByPermalinkException()
    {
        $permalink = $this->createMock(PermalinkInterface::class);
        $permalink->expects(static::once())->method('getParameters')
            ->with(static::equalTo(''))
            ->willThrowException(new \InvalidArgumentException());

        $blog = $this->createMock(BlogInterface::class);
        $blog->expects(static::once())->method('getPermalinkGenerator')->willReturn($permalink);

        $self = $this;
        $result = $this
            ->getPostManager(static function ($qb) {
            })
            ->findOneByPermalink('', $blog);

        static::assertNull($result);
    }

    public function testGetPagerWithoutMode()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithoutModeEnabled()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithoutModeDisabled()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'enabled' => 0,
            ], 1);
    }

    public function testGetPagerWithPublicMode()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'public',
            ], 1);
    }

    public function testGetPagerWithPublicModeEnabled()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'public',
                'enabled' => 1,
            ], 1);
    }

    public function testGetPagerWithPublicModeDisabled()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'mode' => 'public',
                'enabled' => 0,
            ], 1);
    }

    public function testGetPagerWithAdminMode()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $self->assertRelationsJoined($qb);
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([
                'mode' => 'admin',
            ], 1);
    }

    public function testGetPagerWithAdminModeEnabled()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'admin',
                'enabled' => 1,
            ], 1);
    }

    public function testGetPagerWithAdminModeDisabled()
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'mode' => 'admin',
                'enabled' => 0,
            ], 1);
    }

    public function testGetPublicationDateQueryParts()
    {
        $result = $this
            ->getPostManager(static function () {
            })
            ->getPublicationDateQueryParts('2010-02-10', 'month', 'n');

        static::assertNotNull($result);
        static::assertInstanceOf(\DateTimeInterface::class, $result['params']['startDate']);
        static::assertInstanceOf(\DateTimeInterface::class, $result['params']['endDate']);
        static::assertSame('2010-02-10', $result['params']['startDate']->format('Y-m-d'));
        static::assertSame('2010-03-10', $result['params']['endDate']->format('Y-m-d'));
    }

    protected function getPostManager($qbCallback)
    {
        $em = $this->createEntityManagerMock($qbCallback, []);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

        return new PostManager(BasePost::class, $registry);
    }
}
