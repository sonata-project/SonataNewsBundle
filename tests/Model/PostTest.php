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
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\NewsBundle\Model\Post;

class ModelTest_Post extends Post
{
    public function getId(): void
    {
    }
}

class PostTest extends TestCase
{
    public function testSettersGetters(): void
    {
        $date = new \DateTime();

        $post = new ModelTest_Post();
        $post->setAbstract('My abstract content');
        $post->setAuthor('My author');
        $post->setCollection($collection = $this->createMock(CollectionInterface::class));
        $post->setCommentsCloseAt($date);
        $post->setCommentsCount(5);
        $post->setCommentsDefaultStatus(1);
        $post->setCommentsEnabled(true);
        $post->setContent('My content');
        $post->setContentFormatter('markdown');
        $post->setCreatedAt($date);
        $post->setEnabled(true);
        $post->setPublicationDateStart($date);
        $post->setRawContent('My raw content');
        $post->setTags($tags = [$this->createMock(TagInterface::class)]);
        $post->setTitle('My title');
        $post->setSlug('my-post-slug');
        $post->setUpdatedAt($date);

        static::assertSame('My abstract content', $post->getAbstract());
        static::assertSame('My author', $post->getAuthor());
        static::assertSame($collection, $post->getCollection());
        static::assertInstanceOf(CollectionInterface::class, $post->getCollection());
        static::assertSame($date, $post->getCommentsCloseAt());
        static::assertSame(5, $post->getCommentsCount());
        static::assertSame(1, $post->getCommentsDefaultStatus());
        static::assertTrue($post->getCommentsEnabled());
        static::assertSame('My content', $post->getContent());
        static::assertSame('markdown', $post->getContentFormatter());
        static::assertSame($date, $post->getCreatedAt());
        static::assertTrue($post->getEnabled());
        static::assertSame($date, $post->getPublicationDateStart());
        static::assertSame('My raw content', $post->getRawContent());
        static::assertSame('my-post-slug', $post->getSlug());
        static::assertSame($tags, $post->getTags());
        static::assertSame('My title', $post->getTitle());
        static::assertSame($date, $post->getUpdatedAt());
    }
}
