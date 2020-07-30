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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Sonata\NewsBundle\DependencyInjection\Configuration;
use Sonata\NewsBundle\DependencyInjection\SonataNewsExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            'title' => 'Foo title',
            'link' => '/foo/bar',
            'description' => 'Foo description',
            'salt' => 'pepper',
            'permalink_generator' => 'sonata.news.permalink.date',
            'permalink' => [
                'date' => '%%1$04d/%%2$d/%%3$d/%%4$s',
            ],
            'db_driver' => 'doctrine_orm',
            'table' => [
                'post_tag' => 'news__post_tag',
            ],
            'class' => [
                'tag' => 'Application\Sonata\ClassificationBundle\Entity\Tag',
                'collection' => 'Application\Sonata\ClassificationBundle\Entity\Collection',
                'post' => 'Application\Sonata\NewsBundle\Entity\Post',
                'comment' => 'Application\Sonata\NewsBundle\Entity\Comment',
                'media' => 'Application\Sonata\MediaBundle\Entity\Media',
                'user' => 'Application\Sonata\UserBundle\Entity\User',
            ],
            'admin' => [
                'post' => [
                    'class' => 'Sonata\NewsBundle\Admin\PostAdmin',
                    'controller' => 'SonataAdminBundle:CRUD',
                    'translation' => 'SonataNewsBundle',
                ],
                'comment' => [
                    'class' => 'Sonata\NewsBundle\Admin\CommentAdmin',
                    'controller' => 'SonataNewsBundle:CommentAdmin',
                    'translation' => 'SonataNewsBundle',
                ],
            ],
        ], [
            __DIR__.'/../Fixtures/configuration.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new SonataNewsExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
