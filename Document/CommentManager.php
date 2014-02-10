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

use Sonata\CoreBundle\Model\BaseDocumentManager;
use Sonata\NewsBundle\Model\CommentManager as ModelCommentManager;
use Sonata\NewsBundle\Model\CommentInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

use Sonata\DoctrineMongoDBAdminBundle\Datagrid\Pager;
use Sonata\DoctrineMongoDBAdminBundle\Datagrid\ProxyQuery;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;

class CommentManager extends BaseDocumentManager implements CommentManagerInterface
{
    /**
     * @param array   $criteria
     * @param integer $page
     *
     * @return \Sonata\AdminBundle\Datagrid\ODM\Pager
     */
    public function getPager(array $criteria, $page)
    {
        $parameters = array();

        $qb = $this->getDocumentManager()->getRepository($this->class)
            ->createQueryBuilder()
            ->sort('createdAt', 'desc');

        $criteria['status'] = isset($criteria['status']) ? $criteria['status'] : CommentInterface::STATUS_VALID;
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
     * Update the comments count
     *
     * @param \Sonata\NewsBundle\Document\PostInterface $post
     */
    public function updateCommentsCount(PostInterface $post = null)
    {
        $post->setCommentsCount($post->getCommentsCount() + 1);
        $this->getDocumentManager()->persist($post);
        $this->getDocumentManager()->flush();
    }
}
