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
     * @return void
     */
    function getTitle();

    /**
     * @return void
     */
    function getLink();

    /**
     * @return void
     */
    function getDescription();

    /**
     * @param $title
     * @return void
     */
    function setTitle($title);

    /**
     * @param $link
     * @return void
     */
    function setLink($link);

    /**
     * @param $description
     * @return void
     */
    function setDescription($description);

    /**
     * @return void
     *
     * @return \Sonata\NewsBundle\Permalink\PermalinkInterface
     */
    function getPermalinkGenerator();
}