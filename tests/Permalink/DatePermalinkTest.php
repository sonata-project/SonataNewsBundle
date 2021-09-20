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

namespace Sonata\NewsBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Permalink\DatePermalink;

final class DatePermalinkTest extends TestCase
{
    public function testGenerate()
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects(static::any())->method('getSlug')->willReturn('the-slug');
        $post->expects(static::any())->method('getYear')->willReturn('2011');
        $post->expects(static::any())->method('getMonth')->willReturn('12');
        $post->expects(static::any())->method('getDay')->willReturn('30');

        $permalink = new DatePermalink();
        static::assertSame('2011/12/30/the-slug', $permalink->generate($post));
    }

    public function testCustomFormatting()
    {
        $post = $this->createMock(PostInterface::class);
        $post->expects(static::any())->method('getSlug')->willReturn('the-slug');
        $post->expects(static::any())->method('getYear')->willReturn('2011');
        $post->expects(static::any())->method('getMonth')->willReturn('2');
        $post->expects(static::any())->method('getDay')->willReturn('01');

        $permalink = new DatePermalink('%1$02d/%2$02d/%3$02d/%4$s');
        static::assertSame('2011/02/01/the-slug', $permalink->generate($post));
    }

    public function testGetParameters()
    {
        $permalink = new DatePermalink();
        $expected = [
            'year' => 2011,
            'month' => 12,
            'day' => 30,
            'slug' => 'the-slug',
        ];

        static::assertSame($expected, $permalink->getParameters('2011/12/30/the-slug'));
    }

    public function testGetParametersWithWrongUrl()
    {
        $this->expectException('InvalidArgumentException');

        $permalink = new DatePermalink();
        $expected = [
            'year' => '2011',
            'month' => '12',
            'day' => '30',
            'slug' => 'the-slug',
        ];

        static::assertSame($expected, $permalink->getParameters('2011/12/the-slug'));
    }
}
