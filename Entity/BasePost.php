<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Entity;

use Sonata\NewsBundle\Model\Post as ModelPost;
use Doctrine\Common\Collections\ArrayCollection;

abstract class BasePost extends ModelPost
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->tags     = new ArrayCollection();
        $this->comments = new ArrayCollection();

        $this->setPublicationDateStart(new \DateTime);
    }
}
