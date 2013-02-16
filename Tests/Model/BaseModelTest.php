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

class BasePostTest_Post extends \Sonata\NewsBundle\Model\Post
{
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

class BasePostTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCommentable()
    {
        $post = new BasePostTest_Post;

        $post->setEnabled(true);
        $post->setCommentsEnabled(false);
        $this->assertFalse($post->isCommentable());

        $post->setCommentsEnabled(true);

        $past = new \DateTime('-1 hour');
        $post->setCommentsCloseAt($past);
        $this->assertFalse($post->isCommentable());

        $futur = new \DateTime('+1 hour');
        $post->setCommentsCloseAt($futur);
        $this->assertTrue($post->isCommentable());
    }

    public function testIsPublic()
    {
        $post = new BasePostTest_Post;

        $post->setEnabled(true);
        $this->assertTrue($post->isPublic());

        $post->setEnabled(false);
        $this->assertFalse($post->isPublic());

        $post->setEnabled(true);
        $post->setPublicationDateStart(new \DateTime('+1 year'));

        $this->assertFalse($post->isPublic());
    }

    public function testSlug()
    {
        $post = new BasePostTest_Post;

        $post->setTitle('Salut Symfony2');

        $this->assertEquals('salut-symfony2', $post->getSlug());
    }
}
