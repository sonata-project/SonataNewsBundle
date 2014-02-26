<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Tests\Model;

class ModelTest_Post extends \Sonata\NewsBundle\Model\Post
{
    public function getId()
    {

    }
}

/**
 * Class PostTest
 *
 * Tests the post model
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    public function testSettersGetters()
    {
        $date = new \DateTime();

        $post = new ModelTest_Post();
        $post->setAbstract('My abstract content');
        $post->setAuthor('My author');
        $post->setCollection($this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface'));
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
        $post->setTags($this->getMock('Sonata\ClassificationBundle\Model\TagInterface'));
        $post->setTitle('My title');
        $post->setSlug('my-post-slug');
        $post->setUpdatedAt($date);

        $this->assertEquals($post->getAbstract(), 'My abstract content');
        $this->assertEquals($post->getAuthor(), 'My author');
        $this->assertEquals($post->getCollection(), $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface'));
        $this->assertEquals($post->getCommentsCloseAt(), $date);
        $this->assertEquals($post->getCommentsCount(), 5);
        $this->assertEquals($post->getCommentsDefaultStatus(), 1);
        $this->assertEquals($post->getCommentsEnabled(), true);
        $this->assertEquals($post->getContent(), 'My content');
        $this->assertEquals($post->getContentFormatter(), 'markdown');
        $this->assertEquals($post->getCreatedAt(), $date);
        $this->assertEquals($post->getEnabled(), true);
        $this->assertEquals($post->getPublicationDateStart(), $date);
        $this->assertEquals($post->getRawContent(), 'My raw content');
        $this->assertEquals($post->getSlug(), 'my-post-slug');
        $this->assertEquals($post->getTags(), $this->getMock('Sonata\ClassificationBundle\Model\TagInterface'));
        $this->assertEquals($post->getTitle(), 'My title');
        $this->assertEquals($post->getUpdatedAt(), $date);
    }
}
