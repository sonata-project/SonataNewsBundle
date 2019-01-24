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
use Sonata\NewsBundle\Permalink\CollectionPermalink;

class CollectionPermalinkTest extends TestCase
{
    public function testGenerateWithCollection(): void
    {
        $collection = $this->createMock('Sonata\ClassificationBundle\Model\CollectionInterface');
        $collection->expects($this->any())->method('getSlug')->will($this->returnValue('the-collection'));

        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->any())->method('getCollection')->will($this->returnValue($collection));
        $post->expects($this->any())->method('getSlug')->will($this->returnValue('the-slug'));

        $permalink = new CollectionPermalink();
        $this->assertSame('the-collection/the-slug', $permalink->generate($post));
    }

    public function testGenerateWithoutCollection(): void
    {
        $post = $this->createMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->any())->method('getCollection')->will($this->returnValue(null));
        $post->expects($this->any())->method('getSlug')->will($this->returnValue('the-slug'));

        $permalink = new CollectionPermalink();
        $this->assertSame('the-slug', $permalink->generate($post));
    }

    public function testGetParametersWithCollection(): void
    {
        $permalink = new CollectionPermalink();
        $expected = [
            'collection' => 'the-collection',
            'slug' => 'the-slug',
        ];

        $this->assertSame($expected, $permalink->getParameters('the-collection/the-slug'));
    }

    public function testGetParametersWithoutCollection(): void
    {
        $permalink = new CollectionPermalink();
        $expected = [
            'collection' => null,
            'slug' => 'the-slug',
        ];

        $this->assertSame($expected, $permalink->getParameters('the-slug'));
    }

    public function testGetParametersWithoutCollectionAndExtra(): void
    {
        $this->expectException('InvalidArgumentException');

        $permalink = new CollectionPermalink();
        $expected = [
            'collection' => 'the-collection',
            'slug' => 'the-slug',
        ];

        $this->assertSame($expected, $permalink->getParameters('the-collection/the-slug/asdsaasds'));
    }
}
