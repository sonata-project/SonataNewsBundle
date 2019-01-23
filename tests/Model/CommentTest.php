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
use Sonata\NewsBundle\Model\Comment;

class ModelTest_Comment extends Comment
{
    public function getId()
    {
    }
}

class CommentTest extends TestCase
{
    public function testSettersGetters()
    {
        $date = new \DateTime();

        $comment = new ModelTest_Comment();
        $comment->setCreatedAt($date);
        $comment->setEmail('email@example.org');
        $comment->setMessage('My message');
        $comment->setName('My name');
        $comment->setPost($this->createMock('Sonata\NewsBundle\Model\PostInterface'));
        $comment->setStatus(1);
        $comment->setUpdatedAt($date);
        $comment->setUrl('http://www.example.org');

        $this->assertSame($comment->getCreatedAt(), $date);
        $this->assertSame($comment->getEmail(), 'email@example.org');
        $this->assertSame($comment->getMessage(), 'My message');
        $this->assertSame($comment->getName(), 'My name');
        $this->assertSame($comment->getPost(), $this->createMock('Sonata\NewsBundle\Model\PostInterface'));
        $this->assertSame($comment->getStatus(), 1);
        $this->assertSame($comment->getUpdatedAt(), $date);
        $this->assertSame($comment->getUrl(), 'http://www.example.org');
    }
}
