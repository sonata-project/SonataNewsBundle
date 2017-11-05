<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;

class CommentManager extends BaseEntityManager implements CommentManagerInterface
{
    /**
     * @var ManagerInterface
     */
    protected $postManager;

    /**
     * @param string           $class
     * @param ManagerRegistry  $registry
     * @param ManagerInterface $postManager
     */
    public function __construct($class, ManagerRegistry $registry, ManagerInterface $postManager)
    {
        parent::__construct($class, $registry);

        $this->postManager = $postManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save($comment, $andFlush = true)
    {
        parent::save($comment, $andFlush);

        $this->updateCommentsCount($comment->getPost());
    }

    /**
     * Update the number of comment for a comment.
     *
     * @param null|\Sonata\NewsBundle\Model\PostInterface $post
     */
    public function updateCommentsCount(PostInterface $post = null)
    {
        $commentTableName = $this->getObjectManager()->getClassMetadata($this->getClass())->table['name'];
        $postTableName = $this->getObjectManager()->getClassMetadata($this->postManager->getClass())->table['name'];

        $this->getConnection()->beginTransaction();
        $this->getConnection()->query($this->getCommentsCountResetQuery($postTableName));

        $this->getConnection()->query($this->getCommentsCountQuery($postTableName, $commentTableName));

        $this->getConnection()->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($comment, $andFlush = true)
    {
        $post = $comment->getPost();

        parent::delete($comment, $andFlush);

        $this->updateCommentsCount($post);
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }

        $parameters = [];

        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->orderby('c.createdAt', 'DESC');

        if ('public' == $criteria['mode']) {
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
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * @param string $postTableName
     *
     * @return string
     */
    private function getCommentsCountResetQuery($postTableName)
    {
        switch ($this->getConnection()->getDriver()->getDatabasePlatform()->getName()) {
            case 'postgresql':
                return sprintf('UPDATE %s SET comments_count = 0', $postTableName);

            default:
                return sprintf('UPDATE %s p SET p.comments_count = 0', $postTableName);
        }
    }

    /**
     * @param string $postTableName
     * @param string $commentTableName
     *
     * @return string
     */
    private function getCommentsCountQuery($postTableName, $commentTableName)
    {
        switch ($this->getConnection()->getDriver()->getDatabasePlatform()->getName()) {
            case 'postgresql':
                return sprintf(
            'UPDATE %s SET comments_count = count_comment.total
            FROM (SELECT c.post_id, count(*) AS total FROM %s AS c WHERE c.status = 1 GROUP BY c.post_id) count_comment
            WHERE %s.id = count_comment.post_id', $postTableName, $commentTableName, $postTableName);

            default:
                return sprintf(
            'UPDATE %s p, (SELECT c.post_id, count(*) as total FROM %s as c WHERE c.status = 1 GROUP BY c.post_id) as count_comment
            SET p.comments_count = count_comment.total
            WHERE p.id = count_comment.post_id', $postTableName, $commentTableName);
        }
    }
}
