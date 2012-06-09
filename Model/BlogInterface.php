<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use Sonata\NewsBundle\Permalink\PermalinkInterface;

interface BlogInterface
{
    /**
     * @return string
     */
    function getTitle();

    /**
     * @return string
     */
    function getLink();

    /**
     * @return string
     */
    function getDescription();

    /**
     * @param string $title
     */
    function setTitle($title);

    /**
     * @param string $link
     */
    function setLink($link);

    /**
     * @param string $description
     */
    function setDescription($description);

    /**
     * @return \Sonata\NewsBundle\Permalink\PermalinkInterface
     */
    function getPermalinkGenerator();
}