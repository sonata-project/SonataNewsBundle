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

namespace Sonata\NewsBundle\Model;

use Sonata\NewsBundle\Permalink\PermalinkInterface;

class Blog implements BlogInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var PermalinkInterface
     */
    protected $permalinkGenerator;

    /**
     * @param string $title
     * @param string $link
     * @param string $description
     */
    public function __construct($title, $link, $description, PermalinkInterface $permalinkGenerator)
    {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->permalinkGenerator = $permalinkGenerator;
    }

    public function getPermalinkGenerator()
    {
        return $this->permalinkGenerator;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }
}
