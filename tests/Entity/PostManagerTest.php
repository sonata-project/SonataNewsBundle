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

final class PostManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function assertRelationsEnabled($qb): void
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

    public function assertRelationsJoined($qb): void
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

    public function assertPostEnabled($qb, $flag): void
    {
        $qb->expects(static::once())->method('andWhere')->with(static::equalTo('p.enabled = :enabled'));
        $qb->expects(static::once())->method('setParameters')->with(static::equalTo(['enabled' => $flag]));
    }

    public function testFindOneByPermalinkSlug(): void
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
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('p.slug = :slug'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['slug' => 'bar']));
            })
            ->findOneByPermalink('foo/bar', $blog);
    }

    public function testFindOneByPermalinkException(): void
    {
        $permalink = $this->createMock(PermalinkInterface::class);
        $permalink->expects(static::once())->method('getParameters')
            ->with(static::equalTo(''))
            ->willThrowException(new \InvalidArgumentException());

        $blog = $this->createMock(BlogInterface::class);
        $blog->expects(static::once())->method('getPermalinkGenerator')->willReturn($permalink);

        $self = $this;
        $result = $this
            ->getPostManager(static function ($qb): void {
            })
            ->findOneByPermalink('', $blog);

        static::assertNull($result);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithoutMode(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithoutModeEnabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithoutModeDisabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'enabled' => 0,
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithPublicMode(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'public',
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithPublicModeEnabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'public',
                'enabled' => 1,
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithPublicModeDisabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsEnabled($qb);
                $self->assertPostEnabled($qb, 0);
            })
            ->getPager([
                'mode' => 'public',
                'enabled' => 0,
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithAdminMode(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $self->assertRelationsJoined($qb);
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([
                'mode' => 'admin',
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithAdminModeEnabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['p']));
                $self->assertRelationsJoined($qb);
                $self->assertPostEnabled($qb, 1);
            })
            ->getPager([
                'mode' => 'admin',
                'enabled' => 1,
            ], 1);
    }

    /**
     * NEXT_MAJOR: Remove this test.
     *
     * @group legacy
     */
    public function testGetPagerWithAdminModeDisabled(): void
    {
        $self = $this;
        $this
            ->getPostManager(static function ($qb) use ($self): void {
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
            ->getPostManager(static function (): void {
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
