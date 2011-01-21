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

use Doctrine\ORM\EntityRepository;

class BasePostRepository extends EntityRepository
{

    public function findLastPostQueryBuilder($limit) {

        return $this->createQueryBuilder('p')
            ->where('p.enabled = true')
            ->orderby('p.created_at', 'DESC');
        
    }
}