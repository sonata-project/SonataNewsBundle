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

        $this->assertSame('My abstract content', $post->getAbstract());
        $this->assertSame('My author', $post->getAuthor());
        $this->assertSame($collection, $post->getCollection());
        $this->assertInstanceOf(CollectionInterface::class, $post->getCollection());
        $this->assertSame($date, $post->getCommentsCloseAt());
        $this->assertSame(5, $post->getCommentsCount());
        $this->assertSame(1, $post->getCommentsDefaultStatus());
        $this->assertTrue($post->getCommentsEnabled());
        $this->assertSame('My content', $post->getContent());
        $this->assertSame('markdown', $post->getContentFormatter());
        $this->assertSame($date, $post->getCreatedAt());
        $this->assertTrue($post->getEnabled());
        $this->assertSame($date, $post->getPublicationDateStart());
        $this->assertSame('My raw content', $post->getRawContent());
        $this->assertSame('my-post-slug', $post->getSlug());
        $this->assertSame($tags, $post->getTags());
        $this->assertSame('My title', $post->getTitle());
        $this->assertSame($date, $post->getUpdatedAt());
    }
}
