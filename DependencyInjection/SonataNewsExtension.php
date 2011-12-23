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

/**
 * SonataNewsBundleExtension
 *
 * @author      Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataNewsExtension extends Extension
{
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

        $blog = new Definition('Sonata\NewsBundle\Model\Blog', array($config['title'], $config['link'], $config['description'], $config['routing_class']));
        $container->setDefinition('sonata.news.blog', $blog);

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('sonata.news.mailer')
            ->replaceArgument(5, array(
                'notification' => $config['comment']['notification']
            ));
    }
}