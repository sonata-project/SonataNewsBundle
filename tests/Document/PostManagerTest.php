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

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Model\PageableManagerInterface;
use Sonata\NewsBundle\Document\BasePost;
use Sonata\NewsBundle\Document\PostManager;

/**
 * NEXT_MAJOR: Remove this class.
 *
 * @group legacy
 */
class PostManagerTest extends TestCase
{
    public function testImplements()
    {
        $registry = $this->createMock(ManagerRegistry::class);

        $postManager = new PostManager(BasePost::class, $registry);

        static::assertInstanceOf(PageableManagerInterface::class, $postManager);
    }
}
