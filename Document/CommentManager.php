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

use Sonata\AdminBundle\Datagrid\ODM\Pager;
use Sonata\AdminBundle\Datagrid\ODM\ProxyQuery;

class CommentManager extends ModelCommentManager
{
    protected $dm;

    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm    = $dm;
        $this->class = $class;
    }

    public function save(CommentInterface $comment)
    {
        $this->dm->persist($comment);
        $this->dm->flush();
    }

    public function getClass()
    {
        return $this->class;
    }

    public function findOneBy(array $criteria)
    {
        return $this->dm->getRepository($this->class)->findOneBy($criteria);
    }

    public function findBy(array $criteria)
    {
        return $this->dm->getRepository($this->class)->findBy($criteria);
    }

    public function delete(CommentInterface $comment)
    {
        $this->dm->remove($comment);
        $this->dm->flush();
    }

    public function create()
    {
        return new $this->class;
    }

    function getPager(array $criteria, $page)
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