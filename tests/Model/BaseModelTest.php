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
use Sonata\NewsBundle\Model\Post;

class BasePostTest_Post extends Post
{
    public function getId(): void
    {
        // TODO: Implement getId() method.
    }
}

class BasePostTest extends TestCase
{
    public function testIsCommentable(): void
    {
        $post = new BasePostTest_Post();

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

    public function testIsPublic(): void
    {
        $post = new BasePostTest_Post();

        $post->setEnabled(true);
        $this->assertTrue($post->isPublic());

        $post->setEnabled(false);
        $this->assertFalse($post->isPublic());

        $post->setEnabled(true);
        $post->setPublicationDateStart(new \DateTime('+1 year'));

        $this->assertFalse($post->isPublic());
    }

    public function testSlug(): void
    {
        $post = new BasePostTest_Post();

        $post->setTitle('Salut Symfony2');

        $this->assertSame('salut-symfony2', $post->getSlug());
    }
}
