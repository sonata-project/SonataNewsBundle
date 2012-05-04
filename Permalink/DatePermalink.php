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

class DatePermalink implements PermalinkInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(PostInterface $post)
    {
        return sprintf('%d/%d/%d/%s',
            $post->getYear(),
            $post->getMonth(),
            $post->getDay(),
            $post->getSlug()
        );
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @param string $permalink
     *
     * @return array
     */
    public function getParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (count($parameters) != 4) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        list($year, $month, $day, $slug) = $parameters;

        return array(
            'year'  => $year,
            'month' => $month,
            'day'   => $day,
            'slug'  => $slug
        );
    }
}
