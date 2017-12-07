<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
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
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm.xml');
        $loader->load('twig.xml');
        $loader->load('form.xml');
        $loader->load('core.xml');
        $loader->load('serializer.xml');

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.xml');
        }

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
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

        $this->registerDoctrineMapping($config);
        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
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
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
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
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        $collector = DoctrineCollector::getInstance();

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
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
}
