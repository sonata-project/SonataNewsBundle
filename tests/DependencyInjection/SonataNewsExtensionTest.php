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

namespace Sonata\NewsBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\ClassificationBundle\Model\Collection;
use Sonata\ClassificationBundle\Model\Tag;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\MediaBundle\Model\Media;
use Sonata\NewsBundle\DependencyInjection\SonataNewsExtension;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\Post;
use Sonata\NewsBundle\Tests\Fixtures\UserMock;

final class SonataNewsExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', ['SonataDoctrineBundle' => true]);
    }

    public function testLoadWithTagWithCollection(): void
    {
        $this->load($this->getConfigurationWithTagWithCollection());
        $collector = DoctrineCollector::getInstance();
        static::assertCount(2, $collector->getAssociations(), 'Our models should have 2 associations (post, comment)');
        $postManyToOneAssociation = $collector->getAssociations()[Post::class]['mapManyToOne'];
        static::assertCount(3, $postManyToOneAssociation, 'The post model should have 3 many to one associations (user, media, collection)');
        $postManyToManyAssociation = $collector->getAssociations()[Post::class]['mapManyToMany'];
        static::assertCount(1, $postManyToManyAssociation, 'The post model should have 1 many to many association (tag)');
        $postOneToManyAssociation = $collector->getAssociations()[Post::class]['mapOneToMany'];
        static::assertCount(1, $postOneToManyAssociation, 'The post model should have 1 one to many association (comment)');
    }

    protected function getConfigurationWithTagWithCollection(): array
    {
        return [
            'title' => 'Foo title',
            'link' => '/foo/bar',
            'description' => 'Foo description',
            'salt' => 'pepper',
            'comment' => [
                'notification' => [
                    'emails' => ['email@example.org', 'email2@example.org'],
                    'from' => 'no-reply@sonata-project.org',
                    'template' => '@SonataNews/Mail/comment_notification.txt.twig',
                ],
            ],
            'class' => [
                'post' => Post::class,
                'comment' => Comment::class,
                'media' => Media::class,
                'user' => UserMock::class,
                'collection' => Collection::class,
                'tag' => Tag::class,
            ],
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [
            new SonataNewsExtension(),
        ];
    }
}
