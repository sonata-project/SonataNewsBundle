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

class ModelTest_Comment extends \Sonata\NewsBundle\Model\Comment
{
    public function getId()
    {

    }
}

/**
 * Class CommentTest
 *
 * Tests the comment model
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testSettersGetters()
    {
        $date = new \DateTime();

        $comment = new ModelTest_Comment();
        $comment->setCreatedAt($date);
        $comment->setEmail('email@example.org');
        $comment->setMessage('My message');
        $comment->setName('My name');
        $comment->setPost($this->getMock('Sonata\NewsBundle\Model\PostInterface'));
        $comment->setStatus(1);
        $comment->setUpdatedAt($date);
        $comment->setUrl('http://www.example.org');

        $this->assertEquals($comment->getCreatedAt(), $date);
        $this->assertEquals($comment->getEmail(), 'email@example.org');
        $this->assertEquals($comment->getMessage(), 'My message');
        $this->assertEquals($comment->getName(), 'My name');
        $this->assertEquals($comment->getPost(), $this->getMock('Sonata\NewsBundle\Model\PostInterface'));
        $this->assertEquals($comment->getStatus(), 1);
        $this->assertEquals($comment->getUpdatedAt(), $date);
        $this->assertEquals($comment->getUrl(), 'http://www.example.org');
    }
}
