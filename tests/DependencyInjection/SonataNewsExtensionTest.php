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
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Sonata\MediaBundle\Model\Media;
use Sonata\NewsBundle\DependencyInjection\SonataNewsExtension;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\Post;
use Sonata\NewsBundle\Tests\Fixtures\UserMock;

class SonataNewsExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', []);
    }

    /**
     * Test if the deprecation notice is triggered when the tag (or collection) class declaration is missing.
     * It should trigger a deprecation notice but doesn't break anything
     * You should have 0 association.
     *
     * @group legacy
     * @expectedDeprecation The %s class is not defined or does not exist. This is tolerated now but will be forbidden in 4.0
     */
    public function testLoadWithoutTagWithoutCollection(): void
    {
        $this->load($this->getMinimalConfiguration());
        $collector = DoctrineCollector::getInstance();

        $this->assertEmpty($collector->getAssociations(), 'Our models should have 0 association');
    }

    /**
     * Test if the deprecation notice is triggered when the tag (or collection) class declaration is present.
     * It shouldn't trigger a deprecation notice but doesn't break anything
     * You should have 2 associations (Post, Comment).
     * The Post model should have an association with (Media, User, Collection, Tag).
     */
    public function testLoadWithTagWithCollection(): void
    {
        $this->load($this->getConfigurationWithTagWithCollection());
        $collector = DoctrineCollector::getInstance();
        $this->assertCount(2, $collector->getAssociations(), 'Our models should have 2 associations (post, comment)');
        $postManyToOneAssociation = $collector->getAssociations()[Post::class]['mapManyToOne'];
        $this->assertCount(3, $postManyToOneAssociation, 'The post model should have 3 many to one associations (user, media, collection)');
        $postManyToManyAssociation = $collector->getAssociations()[Post::class]['mapManyToMany'];
        $this->assertCount(1, $postManyToManyAssociation, 'The post model should have 1 many to many association (tag)');
        $postOneToManyAssociation = $collector->getAssociations()[Post::class]['mapOneToMany'];
        $this->assertCount(1, $postOneToManyAssociation, 'The post model should have 1 one to many association (comment)');
    }

    protected function getMinimalConfiguration(): array
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
                ],
            ];
    }

    protected function getConfigurationWithTagWithCollection(): array
    {
        $minimalConfiguration = $this->getMinimalConfiguration();
        $tagAndCollectionDeclaration = [
                'class' => [
                        'collection' => Collection::class,
                        'tag' => Tag::class,
                ],
            ];

        return array_merge($minimalConfiguration, $tagAndCollectionDeclaration);
    }

    protected function getContainerExtensions()
    {
        return [
            new SonataNewsExtension(),
        ];
    }
}
