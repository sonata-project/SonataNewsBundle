<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Tests\Document;

use Sonata\NewsBundle\Document\PostManager;
use Sonata\NewsBundle\Tests\PHPUnit_Framework_TestCase;

/**
 * Tests the post manager document.
 */
class PostManagerTest extends PHPUnit_Framework_TestCase
{
    public function testImplements()
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');

        $postManager = new PostManager('Sonata\NewsBundle\Document\BasePost', $registry);

        $this->assertInstanceOf('Sonata\CoreBundle\Model\PageableManagerInterface', $postManager);
    }
}
