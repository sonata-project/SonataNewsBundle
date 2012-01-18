<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * SonataNewsBundleExtension
 *
 * @author      Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataNewsExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('orm.xml');
        $loader->load('twig.xml');
        $loader->load('form.xml');
        $loader->load('core.xml');

        if (!isset($config['salt'])) {
            throw new \InvalidArgumentException("The configration node 'salt' is not set for the SonataNewsbundle (sonata_news)");
        }

        if (!isset($config['comment'])) {
            throw new \InvalidArgumentException("The configration node 'comment' is not set for the SonataNewsbundle (sonata_news)");
        }

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);


        $container->setAlias('sonata.news.permalink.generator', $config['permalink_generator']);

        $container->setDefinition('sonata.news.blog', new Definition('Sonata\NewsBundle\Model\Blog', array(
            $config['title'],
            $config['link'],
            $config['description'],
            new Reference('sonata.news.permalink.generator')
        )));

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('sonata.news.mailer')
            ->replaceArgument(5, array(
                'notification' => $config['comment']['notification']
            ));

        $this->registerDoctrineMapping($config, $container);
        $this->configureClass($config, $container);
    }

    /**
     * @param $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        // admin configuration
        $container->setParameter('sonata.news.admin.post.entity',       $config['class']['post']);
        $container->setParameter('sonata.news.admin.tag.entity',        $config['class']['tag']);
        $container->setParameter('sonata.news.admin.comment.entity',    $config['class']['comment']);
        $container->setParameter('sonata.news.admin.category.entity',   $config['class']['category']);

        // manager configuration
        $container->setParameter('sonata.news.manager.post.entity',     $config['class']['post']);
        $container->setParameter('sonata.news.manager.tag.entity',      $config['class']['tag']);
        $container->setParameter('sonata.news.manager.comment.entity',  $config['class']['comment']);
        $container->setParameter('sonata.news.manager.category.entity', $config['class']['category']);

    }

    /**
     * @param array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        $collector = DoctrineCollector::getInstance();

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector->addAssociation($config['class']['tag'], 'mapManyToMany', array(
            'fieldName'     => 'posts',
            'targetEntity'  => $config['class']['post'],
            'cascade'       => array( ),
            'mappedBy'      => 'tags',
        ));

        $collector->addAssociation($config['class']['post'], 'mapOneToMany', array(
             'fieldName' => 'comments',
             'targetEntity' => $config['class']['comment'],
             'cascade' =>
             array(
                 0 => 'remove',
                 1 => 'persist',
             ),
             'mappedBy' => 'post',
             'orphanRemoval' => true,
             'orderBy' =>
             array(
                 'createdAt' => 'DESC',
             ),
        ));

        $collector->addAssociation($config['class']['post'], 'mapOneToOne', array(
            'fieldName' => 'image',
            'targetEntity' => $config['class']['media'],
            'cascade' =>
            array(
                0 => 'remove',
                1 => 'persist',
                2 => 'refresh',
                3 => 'merge',
                4 => 'detach',
            ),
            'mappedBy' => NULL,
            'inversedBy' => NULL,
            'joinColumns' =>
            array(
                array(
                    'name' => 'image_id',
                    'referencedColumnName' => 'id',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['post'], 'mapOneToOne', array(
             'fieldName' => 'author',
             'targetEntity' => $config['class']['user'],
             'cascade' =>
             array(
                 1 => 'persist',
             ),
             'mappedBy' => NULL,
             'inversedBy' => NULL,
             'joinColumns' =>
             array(
                 array(
                     'name' => 'author_id',
                     'referencedColumnName' => 'id',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['post'], 'mapOneToOne', array(
             'fieldName' => 'category',
             'targetEntity' => $config['class']['category'],
             'cascade' =>
             array(
                 1 => 'persist',
             ),
             'mappedBy' => NULL,
             'inversedBy' => NULL,
             'joinColumns' =>
             array(
                 array(
                     'name' => 'category_id',
                     'referencedColumnName' => 'id',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['post'], 'mapManyToMany', array(
            'fieldName' => 'tags',
            'targetEntity' => $config['class']['tag'],
            'cascade' =>
            array(
                1 => 'persist',
            ),
            'joinTable' =>
            array(
                'name' => 'news__post_tag',
                'joinColumns' =>
                array(
                    array(
                        'name' => 'post_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
                'inverseJoinColumns' =>
                array(
                    array(
                        'name' => 'tag_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
            ),
        ));

        $collector->addAssociation($config['class']['comment'], 'mapOneToOne', array(
             'fieldName' => 'post',
             'targetEntity' => $config['class']['post'],
             'cascade' => array(
             ),
             'mappedBy' => NULL,
             'inversedBy' => NULL,
             'joinColumns' =>
             array(
                 array(
                     'name' => 'post_id',
                     'referencedColumnName' => 'id',
                 ),
             ),
             'orphanRemoval' => false,
        ));
    }
}