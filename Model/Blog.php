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

class Blog implements BlogInterface
{
    protected $title;

    protected $link;

    protected $description;
    
    protected $routingMethod;

    /**
     * @param $title
     * @param $link
     * @param $description
     */
    public function __construct($title, $link, $description, $routingMethod)
    {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->routingMethod = $routingMethod;
    }
    
    public function setRoutingMethod($routingMethod)
    {
        $this->routingMethod = $routingMethod;
    }
    
    public function getRoutingMethod()
    {
        return $this->routingMethod;
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