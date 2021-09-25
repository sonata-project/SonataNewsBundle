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

namespace Sonata\NewsBundle\Document;

use Sonata\Doctrine\Document\BaseDocumentManager;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Pagination\BasePaginator;
use Sonata\NewsBundle\Pagination\MongoDBPaginator;

class CommentManager extends BaseDocumentManager implements CommentManagerInterface
{
    /**
     * Update the comments count.
     *
     * @param PostInterface $post
     */
    public function updateCommentsCount(?PostInterface $post = null): void
    {
        $post->setCommentsCount($post->getCommentsCount() + 1);
        $this->getDocumentManager()->persist($post);
        $this->getDocumentManager()->flush();
    }

    public function getPaginator(array $criteria = [], $page = 1, $limit = 10, array $sort = []): BasePaginator
    {
        $qb = $this->getDocumentManager()->getRepository($this->class)
            ->createQueryBuilder()
            ->sort('createdAt', 'desc');

        $criteria['status'] = $criteria['status'] ?? CommentInterface::STATUS_VALID;
        $qb->field('status')->equals($criteria['status']);

        if (isset($criteria['postId'])) {
            $qb->field('post')->equals($criteria['postId']);
        }

        return (new MongoDBPaginator($qb))->paginate($page);
    }
}
