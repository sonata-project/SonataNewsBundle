<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Document;

use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\Post as ModelPost;
use Sonata\NewsBundle\Model\TagInterface;

abstract class BasePost extends ModelPost
{
    public function __construct()
    {
        $this->tags     = new \Doctrine\Common\Collections\ArrayCollection;
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection;
    }
}