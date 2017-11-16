<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Block\Breadcrumb;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Menu\MenuRegistryInterface;
use Sonata\NewsBundle\Model\BlogInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * BlockService for post breadcrumb.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class NewsPostBreadcrumbBlockService extends BaseNewsBreadcrumbBlockService
{
    /**
     * @var BlogInterface
     */
    protected $blog;

    /**
     * @param string                      $context
     * @param string                      $name
     * @param EngineInterface             $templating
     * @param MenuProviderInterface       $menuProvider
     * @param FactoryInterface            $factory
     * @param BlogInterface               $blog
     * @param MenuRegistryInterface|array $menuRegistry
     *
     * NEXT_MAJOR: Use MenuRegistryInterface as a type of $menuRegistry argument
     */
    public function __construct($context, $name, EngineInterface $templating, MenuProviderInterface $menuProvider, FactoryInterface $factory, BlogInterface $blog, $menuRegistry = [])
    {
        $this->blog = $blog;

        /*
         * NEXT_MAJOR: Remove if statements
         */
        if (!$menuRegistry instanceof MenuRegistryInterface && !is_array($menuRegistry)) {
            throw new \InvalidArgumentException(sprintf(
                'MenuRegistry must be either type of array or instance of %s',
                MenuRegistryInterface::class
            ));
        } elseif (is_array($menuRegistry)) {
            @trigger_error(sprintf(
                'Initializing %s without menuRegistry parameter is deprecated since 2.x and will'.
                ' be removed in 3.0. Use an instance of %s as last argument.',
                __CLASS__,
                MenuRegistryInterface::class
            ), E_USER_DEPRECATED);
        }

        parent::__construct($context, $name, $templating, $menuProvider, $factory, $menuRegistry);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.news.block.breadcrumb_post';
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        parent::configureSettings($resolver);

        $resolver->setDefaults([
            'post' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        if ($post = $blockContext->getBlock()->getSetting('post')) {
            $menu->addChild($post->getTitle(), [
                'route' => 'sonata_news_view',
                'routeParameters' => [
                    'permalink' => $this->blog->getPermalinkGenerator()->generate($post),
                ],
            ]);
        }

        return $menu;
    }
}
