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
    public function getId()
    {
    }
}

/**
 * Tests the post model.
 */
class PostTest extends TestCase
{
    public function testSettersGetters()
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

        $this->assertSame($post->getAbstract(), 'My abstract content');
        $this->assertSame($post->getAuthor(), 'My author');
        $this->assertSame($post->getCollection(), $collection);
        $this->assertInstanceOf(CollectionInterface::class, $post->getCollection());
        $this->assertSame($post->getCommentsCloseAt(), $date);
        $this->assertSame($post->getCommentsCount(), 5);
        $this->assertSame($post->getCommentsDefaultStatus(), 1);
        $this->assertSame($post->getCommentsEnabled(), true);
        $this->assertSame($post->getContent(), 'My content');
        $this->assertSame($post->getContentFormatter(), 'markdown');
        $this->assertSame($post->getCreatedAt(), $date);
        $this->assertSame($post->getEnabled(), true);
        $this->assertSame($post->getPublicationDateStart(), $date);
        $this->assertSame($post->getRawContent(), 'My raw content');
        $this->assertSame($post->getSlug(), 'my-post-slug');
        $this->assertSame($post->getTags(), $tags);
        $this->assertSame($post->getTitle(), 'My title');
        $this->assertSame($post->getUpdatedAt(), $date);
    }
}
