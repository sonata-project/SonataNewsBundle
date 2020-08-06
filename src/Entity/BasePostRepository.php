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

namespace Sonata\NewsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sonata\NewsBundle\Model\CommentInterface;

class BasePostRepository extends EntityRepository
{
    /**
     * return last post query builder.
     *
     * @param int $limit
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findLastPostQueryBuilder($limit)
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = true')
            ->orderBy('p.createdAt', 'DESC');
    }

    /**
     * return count comments QueryBuilder.
     *
     * @param PostInterface $post
     *
     * @return QueryBuilder
     */
    public function countCommentsQuery($post)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->select('COUNT(c.id)')
            ->where($qb->expr()->eq('c.status', ':status'))
            ->andWhere($qb->expr()->eq('c.post', ':post'))
            ->setParameters([
                'status' => CommentInterface::STATUS_VALID,
                'post' => $post,
            ]);
    }
}
