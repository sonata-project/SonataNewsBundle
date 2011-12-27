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

interface PermalinkInterface
{
    /**
     * @abstract
     * @param Sonata\NewsBundle\Model\PostInterface $post
     */
    function generate(PostInterface $post);
}