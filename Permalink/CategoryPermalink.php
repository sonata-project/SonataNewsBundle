<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Permalink;

use Sonata\NewsBundle\Model\PostInterface;

class CategoryPermalink implements PermalinkInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(PostInterface $post)
    {
        return null == $post->getCategory()
            ? $post->getSlug()
            : sprintf('%s/%s', $post->getCategory()->getSlug(), $post->getSlug());
    }

    /**
     * @param string $permalink
     *
     * @return array
     */
    public function getParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (count($parameters) > 2 || count($parameters) == 0) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        if (false === strpos($permalink, '/')) {
            $category = null;
            $slug = $permalink;
        } else {
            list($category, $slug) = $parameters;
        }

        return array(
            'category' => $category,
            'slug'     => $slug,
        );
    }
}
