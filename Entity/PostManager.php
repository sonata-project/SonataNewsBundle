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

use Sonata\NewsBundle\Model\PostManager as ModelPostManager;
use Sonata\NewsBundle\Model\PostInterface;

use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;

class PostManager extends ModelPostManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function save(PostInterface $post)
    {
        $this->em->persist($post);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $slug
     *
     * @return \Sonata\NewsBundle\Model\PostInterface|null
     */
    public function findOneBySlug($year, $month, $day, $slug)
    {
        try {
            $pdqp = $this->getPublicationDateQueryParts(sprintf('%s-%s-%s', $year, $month, $day), 'day');

            return $this->em->getRepository($this->class)
                ->createQueryBuilder('p')
                ->where('p.slug = :slug')
                ->andWhere($pdqp['query'])
                ->setParameters(array_merge($pdqp['params'], array('slug' => $slug)))
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     * @param $permalink
     * 
     * @return \Sonata\NewsBundle\Model\PostInterface|null
     */
    public function findOneByPermalink($permalink)
    {
        try {
            $category = array(
                'query' => '',
                'params' => array()
            );
            
            if (false === strpos($permalink, '/')) {
                $category['query'] = 'p.category IS NULL';
                $slug = $permalink;
            } else {
                $category['query'] = 'c.slug = :category';
                list($category['params']['category'], $slug) = explode('/', $permalink);
            }

            return $this->em->getRepository($this->class)
                ->createQueryBuilder('p')
                ->leftJoin('p.category', 'c')
                ->where('p.slug = :slug')
                ->andWhere($category['query'])
                ->setParameters(array_merge($category['params'], array('slug' => $slug)))
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
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
    public function delete(PostInterface $post)
    {
        $this->em->remove($post);
        $this->em->flush();
    }

    /**
     * Retrieve posts, based on the criteria, a page at a time.
     * Valid criteria are:
     *    enabled - boolean
     *    date - query
     *    tag - string
     *    author - 'NULL', 'NOT NULL', id, array of ids
     *
     * @param array $criteria
     * @param integer $page
     *
     * @return \Sonata\AdminBundle\Datagrid\ORM\Pager
     */
    public function getPager(array $criteria, $page)
    {
        $parameters = array();
        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('p')
            ->select('p, t')
            ->leftJoin('p.tags', 't', Expr\Join::WITH, 't.enabled = true')
            ->leftJoin('p.author', 'a', Expr\Join::WITH, 'a.enabled = true')
            ->orderby('p.publicationDateStart', 'DESC');

        // enabled
        $criteria['enabled'] = isset($criteria['enabled']) ? $criteria['enabled'] : true;
        $query->andWhere('p.enabled = :enabled');
        $parameters['enabled'] = $criteria['enabled'];

        if (isset($criteria['date'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

        if (isset($criteria['tag'])) {
            $query->andWhere('t.slug LIKE :tag');
            $parameters['tag'] = (string)$criteria['tag'];
        }

        if (isset($criteria['author'])) {
            if (!is_array($criteria['author']) && stristr($criteria['author'], 'NULL')) {
                $query->andWhere('p.author IS '.$criteria['author']);
            } else {
                $query->andWhere(sprintf('p.author IN (%s)', implode((array)$criteria['author'], ',')));
            }
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * @param string $date  Date in format YYYY-MM-DD
     * @param string $step  Interval step: year|month|day
     * @param string $alias Table alias for the publicationDateStart column
     *
     * @return array
     */
    public function getPublicationDateQueryParts($date, $step, $alias = 'p')
    {
        return array(
            'query'  => sprintf('%s.publicationDateStart >= :startDate AND %s.publicationDateStart < :endDate', $alias, $alias),
            'params' => array(
                'startDate' => new \DateTime($date),
                'endDate'   => new \DateTime($date . '+1 ' . $step)
            )
        );
    }
}
