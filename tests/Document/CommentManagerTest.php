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

namespace Sonata\NewsBundle\Tests\Document;

use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Model\PageableManagerInterface;
use Sonata\NewsBundle\Document\BaseComment;
use Sonata\NewsBundle\Document\CommentManager;
use Sonata\NewsBundle\Model\PostManagerInterface;

/**
 * Tests the comment manager document.
 */
class CommentManagerTest extends TestCase
{
    public function testImplements()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $postManager = $this->createMock(PostManagerInterface::class);

        $commentManager = new CommentManager(BaseComment::class, $registry, $postManager);

        $this->assertInstanceOf(PageableManagerInterface::class, $commentManager);
    }
}
