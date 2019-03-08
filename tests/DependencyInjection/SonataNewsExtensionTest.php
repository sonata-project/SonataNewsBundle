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
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Sonata\MediaBundle\Model\Media;
use Sonata\NewsBundle\DependencyInjection\SonataNewsExtension;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\Post;
use Sonata\UserBundle\Model\User;

class SonataNewsExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', []);
    }

    /**
     * Test if the deprecation notice is trigger when the tag (or collection) class declaration is missing.
     * It should trigger a deprecation notice but doesn't break anything
     * You should have 0 associations.
     *
     * @group legacy
     * @expectedDeprecation The class %s is not defined or doesn't exist. This is tolerated now but will be forbidden in 4.0
     */
    public function testLoadWithoutTagWithoutCollection(): void
    {
        $this->load($this->getMinimalConfiguration());
        $collector = DoctrineCollector::getInstance();

        $this->assertEmpty($collector->getAssociations());
    }

    /**
     * Test if the deprecation notice is trigger when the tag (or collection) class declaration is present.
     * It shouldn't trigger a deprecation notice but doesn't break anything
     * You should have 2 associations (Post, Comment).
     * The Post model should have an associations with (Media, User, Collection, Tag).
     */
    public function testLoadWithTagWithCollection(): void
    {
        $minimalConfiguration = $this->getMinimalConfiguration();
        $tagAndCollectionDeclaration = [
                'class' => [
                        'collection' => \stdClass::class,
                        'tag' => \stdClass::class,
                ],
            ];
        $this->load(array_merge($minimalConfiguration, $tagAndCollectionDeclaration));
        $collector = DoctrineCollector::getInstance();
        //assert our model have associations
        $this->assertSame(2, count($collector->getAssociations()));
        $postManyToOneAssociation = $collector->getAssociations()[Post::class]['mapManyToOne'];
        //assert the post model has  3 many to one associations (user,media,collection)
        $this->assertSame(3, count($postManyToOneAssociation));
        $postManyToManyAssociation = $collector->getAssociations()[Post::class]['mapManyToMany'];
        //assert the post model has  1 many to many associations (tag)
        $this->assertSame(1, count($postManyToManyAssociation));
        $postOneToManyAssociation = $collector->getAssociations()[Post::class]['mapOneToMany'];
        //assert the post model has  1 one to many associations (comment)
        $this->assertSame(1, count($postOneToManyAssociation));
    }

    public function getMinimalConfiguration(): array
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
                    'user' => User::class,
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [
            new SonataNewsExtension(),
        ];
    }
}
