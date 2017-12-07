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

use PHPUnit\Framework\TestCase;
use Sonata\NewsBundle\Document\CommentManager;

/**
 * Tests the comment manager document.
 */
class CommentManagerTest extends TestCase
{
    public function testImplements(): void
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $postManager = $this->createMock('Sonata\NewsBundle\Model\PostManagerInterface');

        $commentManager = new CommentManager('Sonata\NewsBundle\Document\BaseComment', $registry, $postManager);

        $this->assertInstanceOf('Sonata\CoreBundle\Model\PageableManagerInterface', $commentManager);
    }
}
