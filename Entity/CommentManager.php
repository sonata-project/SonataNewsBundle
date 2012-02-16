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

use Sonata\NewsBundle\Model\CommentManager as ModelCommentManager;
use Sonata\NewsBundle\Model\CommentInterface;
use Doctrine\ORM\EntityManager;

use Sonata\NewsBundle\Model\PostManagerInterface;

use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class CommentManager extends ModelCommentManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Sonata\NewsBundle\Model\PostManagerInterface
     */
    protected $postManager;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     * @param \Sonata\NewsBundle\Model\PostManagerInterface $postManager
     */
    public function __construct(EntityManager $em, $class, $postManager)
    {
        $this->em           = $em;
        $this->postManager  = $postManager;
        $this->class        = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function save(CommentInterface $comment)
    {
        $this->em->persist($comment);
        $this->em->flush();

        $post = $comment->getPost();
        $post->setCommentsCount($this->postManager->countComments($post));
        $this->postManager->save($post);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(CommentInterface $comment)
    {
        $this->em->remove($comment);
        $this->em->flush();
    }

    /**
     * @param array $criteria
     * @param integer $page
     *
     * @return \Sonata\AdminBundle\Datagrid\ORM\Pager
     */
    public function getPager(array $criteria, $page)
    {
        $parameters = array();

        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('c')
            ->orderby('c.createdAt', 'DESC');

        $criteria['status'] = isset($criteria['status']) ? $criteria['status'] : CommentInterface::STATUS_VALID;
        $query->andWhere('c.status = :status');
        $parameters['status'] = $criteria['status'];

        if (isset($criteria['postId'])) {
            $query->andWhere('c.post = :postId');
            $parameters['postId'] = $criteria['postId'];
        }

        $query->setParameters($parameters);

        $pager = new Pager(500); // no limit
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
