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

namespace Sonata\NewsBundle\DependencyInjection;

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector as DeprecatedDoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataNewsExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('actions.xml');
        $loader->load('twig.xml');
        $loader->load('form.xml');
        $loader->load('core.xml');
        $loader->load('serializer.xml');
        $loader->load('command.xml');

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.xml');
        }

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load(sprintf('api_form_%s.xml', $config['db_driver']));
            if ('doctrine_orm' === $config['db_driver']) {
                $loader->load('api_controllers.xml');
            }
        }

        $loader->load(sprintf('%s.xml', $config['db_driver']));

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load(sprintf('%s_admin.xml', $config['db_driver']));
        }

        if (!isset($config['salt'])) {
            throw new \InvalidArgumentException(
                'The configuration node "salt" is not set for the SonataNewsBundle (sonata_news)'
            );
        }

        if (!isset($config['comment'])) {
            throw new \InvalidArgumentException(
                'The configuration node "comment" is not set for the SonataNewsBundle (sonata_news)'
            );
        }

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('sonata.news.permalink.date')
            ->replaceArgument(0, $config['permalink']['date']);

        $container->setAlias('sonata.news.permalink.generator', $config['permalink_generator']);

        $container->setDefinition('sonata.news.blog', new Definition('Sonata\NewsBundle\Model\Blog', [
            $config['title'],
            $config['link'],
            $config['description'],
            new Reference('sonata.news.permalink.generator'),
        ]));

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('sonata.news.mailer')
            ->replaceArgument(5, [
                'notification' => $config['comment']['notification'],
            ]);

        if ('doctrine_orm' === $config['db_driver']) {
            if (isset($bundles['SonataDoctrineBundle'])) {
                $this->registerSonataDoctrineMapping($config);
            } else {
                // NEXT MAJOR: Remove next line and throw error when not registering SonataDoctrineBundle
                $this->registerDoctrineMapping($config);
            }
        }

        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array $config
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        // admin configuration
        $container->setParameter('sonata.news.admin.post.entity', $config['class']['post']);
        $container->setParameter('sonata.news.admin.comment.entity', $config['class']['comment']);

        // manager configuration
        $container->setParameter('sonata.news.manager.post.entity', $config['class']['post']);
        $container->setParameter('sonata.news.manager.comment.entity', $config['class']['comment']);
    }

    /**
     * @param array $config
     */
    public function configureAdmin($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.news.admin.post.class', $config['admin']['post']['class']);
        $container->setParameter('sonata.news.admin.post.controller', $config['admin']['post']['controller']);
        $container->setParameter('sonata.news.admin.post.translation_domain', $config['admin']['post']['translation']);

        $container->setParameter('sonata.news.admin.comment.class', $config['admin']['comment']['class']);
        $container->setParameter('sonata.news.admin.comment.controller', $config['admin']['comment']['controller']);
        $container->setParameter('sonata.news.admin.comment.translation_domain', $config['admin']['comment']['translation']);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     */
    public function registerDoctrineMapping(array $config)
    {
        @trigger_error(
            'Using SonataEasyExtendsBundle is deprecated since sonata-project/news-bundle 3.14. Please register SonataDoctrineBundle as a bundle instead.',
            E_USER_DEPRECATED
        );

        $collector = DeprecatedDoctrineCollector::getInstance();

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                /*
                 * NEXT_MAJOR:
                 * Throw an exception if the class is not defined
                 */
                @trigger_error(sprintf(
                    'The "%s" class is not defined or does not exist. This is tolerated now but will be forbidden in 4.0',
                    $class
                ), E_USER_DEPRECATED);

                return;
            }
        }

        $collector->addAssociation($config['class']['post'], 'mapOneToMany', [
            'fieldName' => 'comments',
            'targetEntity' => $config['class']['comment'],
            'cascade' => [
                    0 => 'remove',
                    1 => 'persist',
                ],
            'mappedBy' => 'post',
            'orphanRemoval' => true,
            'orderBy' => [
                    'createdAt' => 'DESC',
                ],
        ]);

        $collector->addAssociation($config['class']['post'], 'mapManyToOne', [
            'fieldName' => 'image',
            'targetEntity' => $config['class']['media'],
            'cascade' => [
                    0 => 'remove',
                    1 => 'persist',
                    2 => 'refresh',
                    3 => 'merge',
                    4 => 'detach',
                ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                    [
                        'name' => 'image_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['post'], 'mapManyToOne', [
            'fieldName' => 'author',
            'targetEntity' => $config['class']['user'],
            'cascade' => [
                    1 => 'persist',
                ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                    [
                        'name' => 'author_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['post'], 'mapManyToOne', [
            'fieldName' => 'collection',
            'targetEntity' => $config['class']['collection'],
            'cascade' => [
                    1 => 'persist',
                ],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                    [
                        'name' => 'collection_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['post'], 'mapManyToMany', [
            'fieldName' => 'tags',
            'targetEntity' => $config['class']['tag'],
            'cascade' => [
                    1 => 'persist',
                ],
            'joinTable' => [
                    'name' => $config['table']['post_tag'],
                    'joinColumns' => [
                            [
                                'name' => 'post_id',
                                'referencedColumnName' => 'id',
                            ],
                        ],
                    'inverseJoinColumns' => [
                            [
                                'name' => 'tag_id',
                                'referencedColumnName' => 'id',
                            ],
                        ],
                ],
        ]);

        $collector->addAssociation($config['class']['comment'], 'mapManyToOne', [
            'fieldName' => 'post',
            'targetEntity' => $config['class']['post'],
            'cascade' => [
            ],
            'mappedBy' => null,
            'inversedBy' => 'comments',
            'joinColumns' => [
                    [
                        'name' => 'post_id',
                        'referencedColumnName' => 'id',
                        'nullable' => false,
                    ],
                ],
            'orphanRemoval' => false,
        ]);
    }

    private function registerSonataDoctrineMapping(array $config): void
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['post'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('comments', $config['class']['comment'])
                ->cascade(['remove', 'persist'])
                ->mappedBy('post')
                ->orphanRemoval()
                ->addOrder('createdAt', 'DESC')
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('image', $config['class']['media'])
                ->cascade(['remove', 'persist', 'refresh', 'merge', 'detach'])
                ->addJoin([
                    'name' => 'image_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('author', $config['class']['user'])
                ->cascade(['persist'])
                ->addJoin([
                    'name' => 'author_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('collection', $config['class']['collection'])
                ->cascade(['persist'])
                ->addJoin([
                    'name' => 'collection_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToMany',
            OptionsBuilder::createManyToMany('tags', $config['class']['tag'])
                ->cascade(['persist'])
                ->addJoinTable($config['table']['post_tag'], [[
                    'name' => 'post_id',
                    'referencedColumnName' => 'id',
                ]], [[
                    'name' => 'tag_id',
                    'referencedColumnName' => 'id',
                ]])
        );

        $collector->addAssociation(
            $config['class']['comment'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('post', $config['class']['post'])
                ->inversedBy('comments')
                ->addJoin([
                    'name' => 'post_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                ])
        );
    }
}
