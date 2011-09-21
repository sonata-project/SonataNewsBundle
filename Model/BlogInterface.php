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

interface BlogInterface
{

    /**
     * @abstract
     * @return void
     */
    function getTitle();

    /**
     * @abstract
     * @return void
     */
    function getLink();

    /**
     * @abstract
     * @return void
     */
    function getDescription();

    /**
     * @abstract
     * @param $title
     * @return void
     */
    function setTitle($title);

    /**
     * @abstract
     * @param $link
     * @return void
     */
    function setLink($link);

    /**
     * @abstract
     * @param $description
     * @return void
     */
    function setDescription($description);
}