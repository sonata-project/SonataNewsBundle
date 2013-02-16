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
    public function getTitle();

    /**
     * @return string
     */
    public function getLink();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @param string $link
     */
    public function setLink($link);

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return \Sonata\NewsBundle\Permalink\PermalinkInterface
     */
    public function getPermalinkGenerator();
}
