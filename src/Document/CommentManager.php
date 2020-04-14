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
use Sonata\DoctrineMongoDBAdminBundle\Datagrid\Pager;
use Sonata\DoctrineMongoDBAdminBundle\Datagrid\ProxyQuery;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;

class CommentManager extends BaseDocumentManager implements CommentManagerInterface
{
    /**
     * @param int $page
     * @param int $limit
     *
     * @return Pager
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $qb = $this->getDocumentManager()->getRepository($this->class)
            ->createQueryBuilder()
            ->sort('createdAt', 'desc');

        $criteria['status'] = $criteria['status'] ?? CommentInterface::STATUS_VALID;
        $qb->field('status')->equals($criteria['status']);

        if (isset($criteria['postId'])) {
            $qb->field('post')->equals($criteria['postId']);
        }

        $pager = new Pager(500); // no limit
        $pager->setQuery(new ProxyQuery($qb));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * Update the comments count.
     *
     * @param PostInterface $post
     */
    public function updateCommentsCount(?PostInterface $post = null)
    {
        $post->setCommentsCount($post->getCommentsCount() + 1);
        $this->getDocumentManager()->persist($post);
        $this->getDocumentManager()->flush();
    }
}
