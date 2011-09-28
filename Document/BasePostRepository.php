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

use Doctrine\ODM\MongoDB\DocumentRepository;

class BasePostRepository extends DocumentRepository
{
    public function findLastPostQueryBuilder($limit) {

        return $this->createQueryBuilder()
            ->field('enabled')->equals(true)
            ->sort('createdAt', 'desc')
            ->limit($limit)
            ->getQuery()
            ->execute();
    }
}