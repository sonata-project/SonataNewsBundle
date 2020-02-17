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

namespace Sonata\NewsBundle\Permalink;

use Sonata\NewsBundle\Model\PostInterface;

class DatePermalink implements PermalinkInterface
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param $pattern
     */
    public function __construct($pattern = '%1$04d/%2$d/%3$d/%4$s')
    {
        $this->pattern = $pattern;
    }

    public function generate(PostInterface $post)
    {
        return sprintf(
            $this->pattern,
            $post->getYear(),
            $post->getMonth(),
            $post->getDay(),
            $post->getSlug()
        );
    }

    public function getParameters($permalink)
    {
        $parameters = explode('/', $permalink);

        if (4 !== \count($parameters)) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        list($year, $month, $day, $slug) = $parameters;

        return [
            'year' => (int) $year,
            'month' => (int) $month,
            'day' => (int) $day,
            'slug' => $slug,
        ];
    }
}
