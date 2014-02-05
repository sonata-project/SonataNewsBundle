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

use Doctrine\DBAL\Connection;

use Sonata\NewsBundle\Model\PostManager as BasePostManager;

class PostManager extends BasePostManager
{
    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->om->getConnection();
    }
}
