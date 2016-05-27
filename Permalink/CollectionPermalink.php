<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Permalink;

use Sonata\NewsBundle\Model\PostInterface;

class CollectionPermalink implements PermalinkInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(PostInterface $post)
    {
        return null == $post->getCollection()
            ? $post->getSlug()
            : sprintf('%s/%s', $post->getCollection()->getSlug(), $post->getSlug());
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (count($parameters) > 2 || count($parameters) == 0) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        if (false === strpos($permalink, '/')) {
            $collection = null;
            $slug = $permalink;
        } else {
            list($collection, $slug) = $parameters;
        }

        return array(
            'collection' => $collection,
            'slug' => $slug,
        );
    }
}
