<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Block\Breadcrumb;

use Sonata\BlockBundle\Block\BlockContextInterface;

/**
 * BlockService for archive breadcrumb
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class NewsArchiveBreadcrumbBlockService extends BaseNewsBreadcrumbBlockService
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata.news.block.breadcrumb_archive';
    }

    /**
     * {@inheritdoc}
     */
    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        if ($collection = $blockContext->getBlock()->getSetting('collection')) {
            $menu->addChild($collection->getName(), array(
                'route'           => 'sonata_news_collection',
                'routeParameters' => array(
                    'collection' => $collection->getSlug(),
                ),
            ));
        }

        if ($tag = $blockContext->getBlock()->getSetting('tag')) {
            $menu->addChild($tag->getName(), array(
                'route'           => 'sonata_news_tag',
                'routeParameters' => array(
                    'tag' => $tag->getSlug(),
                ),
            ));
        }

        return $menu;
    }
}
