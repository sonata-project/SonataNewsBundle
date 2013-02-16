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

use Sonata\NewsBundle\Model\CommentManager as ModelCommentManager;
use Sonata\NewsBundle\Model\CommentInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

use Sonata\DoctrineMongoDBAdminBundle\Datagrid\Pager;
use Sonata\DoctrineMongoDBAdminBundle\Datagrid\ProxyQuery;

class CommentManager extends ModelCommentManager
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     * @param string          $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm    = $dm;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CommentInterface $comment)
    {
        $this->dm->persist($comment);
        $this->dm->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->dm->getRepository($this->class)->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        return $this->dm->getRepository($this->class)->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(CommentInterface $comment)
    {
        $this->dm->remove($comment);
        $this->dm->flush();
    }

    /**
     * @param array   $criteria
     * @param integer $page
     *
     * @return \Sonata\AdminBundle\Datagrid\ODM\Pager
     */
    public function getPager(array $criteria, $page)
    {
        $parameters = array();

        $qb = $this->dm->getRepository($this->class)
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

}
