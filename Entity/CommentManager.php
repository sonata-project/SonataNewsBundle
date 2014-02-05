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

use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\NewsBundle\Model\CommentInterface;
use Doctrine\ORM\EntityManager;

use Sonata\NewsBundle\Model\PostInterface;

use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

use Sonata\NewsBundle\Model\CommentManager as BaseCommentManager;

class CommentManager extends BaseCommentManager
{
    /**
     * @var ManagerInterface
     */
    protected $postManager;

    /**
     * Constructor.
     *
     * @param string           $class
     * @param EntityManager    $em
     * @param ManagerInterface $postManager
     */
    public function __construct($class, EntityManager $em, ManagerInterface $postManager)
    {
        parent::__construct($class, $em);

        $this->postManager = $postManager;
    }

    /**
     * {@inheritDoc}
     */
    public function save($comment, $andFlush = true)
    {
        parent::save($comment, $andFlush);

        $this->updateCommentsCount($comment->getPost());
    }

    /**
     * Update the number of comment for a comment
     *
     * @param null|\Sonata\NewsBundle\Model\PostInterface $post
     *
     * @return void
     */
    public function updateCommentsCount(PostInterface $post = null)
    {
        $commentTableName = $this->om->getClassMetadata($this->getClass())->table['name'];
        $postTableName    = $this->om->getClassMetadata($this->postManager->getClass())->table['name'];

        $this->om->getConnection()->beginTransaction();
        $this->om->getConnection()->query(sprintf('UPDATE %s p SET p.comments_count = 0' , $postTableName));

        $this->om->getConnection()->query(sprintf(
            'UPDATE %s p, (SELECT c.post_id, count(*) as total FROM %s as c WHERE c.status = 1 GROUP BY c.post_id) as count_comment
            SET p.comments_count = count_comment.total
            WHERE p.id = count_comment.post_id'
        , $postTableName, $commentTableName));

        $this->om->getConnection()->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function delete($comment, $andFlush = true)
    {
        $post = $comment->getPost();

        parent::delete($comment, $andFlush);

        $this->updateCommentsCount($post);
    }

    /**
     * @param array   $criteria
     * @param integer $page
     * @param integer $maxPerPage
     *
     * @return \Sonata\AdminBundle\Datagrid\ORM\Pager
     */
    public function getPager(array $criteria, $page, $maxPerPage = 10)
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }

        $parameters = array();

        $query = $this->om->getRepository($this->class)
            ->createQueryBuilder('c')
            ->orderby('c.createdAt', 'DESC');

        if ($criteria['mode'] == 'public') {
            $criteria['status'] = isset($criteria['status']) ? $criteria['status'] : CommentInterface::STATUS_VALID;
            $query->andWhere('c.status = :status');
            $parameters['status'] = $criteria['status'];
        }

        if (isset($criteria['postId'])) {
            $query->andWhere('c.post = :postId');
            $parameters['postId'] = $criteria['postId'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($maxPerPage);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->om->getConnection();
    }


}
