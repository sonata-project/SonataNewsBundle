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
use Sonata\NewsBundle\Model\Post;
use Sonata\NewsBundle\Permalink\PermalinkInterface;
use Sonata\NewsBundle\Model\BlogInterface;

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
     * @param $permalink
     * @param \Sonata\NewsBundle\Model\BlogInterface $blog
     * @return null
     */
    public function findOneByPermalink($permalink, BlogInterface $blog)
    {
        try {
            $repository = $this->em->getRepository($this->class);

            $query = $repository->createQueryBuilder('p');

            $urlParameters = $blog->getPermalinkGenerator()->getParameters($permalink);

            $parameters = array();

            if (isset($urlParameters['year']) && isset($urlParameters['month']) && isset($urlParameters['day'])) {
                $pdqp = $this->getPublicationDateQueryParts(sprintf('%d-%d-%d', $urlParameters['year'], $urlParameters['month'], $urlParameters['day']), 'day');

                $parameters = array_merge($parameters, $pdqp['params']);

                $query->andWhere($pdqp['query']);
            }

            if (isset($urlParameters['slug'])) {
                $query->andWhere('p.slug = :slug');
                $parameters['slug'] = $urlParameters['slug'];
            }

            if (array_key_exists('category', $urlParameters)) {
                $pcqp = $this->getPublicationCategoryQueryParts($urlParameters['category']);

                $parameters = array_merge($parameters, $pcqp['params']);

                $query
                    ->leftJoin('p.category', 'c')
                    ->andWhere($pcqp['query'])
                ;
            }

            if (count($parameters) == 0) {
                return null;
            }

            $query->setParameters($parameters);

            return $query->getQuery()->getSingleResult();

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
            ->leftJoin('p.category', 'c', Expr\Join::WITH, 'c.enabled = true')
            ->orderby('p.publicationDateStart', 'DESC');

        // enabled
        $criteria['enabled'] = isset($criteria['enabled']) ? $criteria['enabled'] : true;
        $query->andWhere('p.enabled = :enabled');
        $parameters['enabled'] = $criteria['enabled'];
        
        $query->andWhere('p.publicationDateStart <= :now');
        $parameters['now'] = new \DateTime;
        
        $query->andWhere('c.enabled = true OR p.category IS NULL');

        if (isset($criteria['date'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

        if (isset($criteria['tag'])) {
            $query->andWhere('t.slug LIKE :tag');
            $parameters['tag'] = (string)$criteria['tag'];
        }
        
        if (isset($criteria['category'])) {
            $query->andWhere('c.slug = :category');
            $parameters['category'] = (string)$criteria['category'];
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

    /**
     * @param string $category
     *
     * @return array
     */
    public function getPublicationCategoryQueryParts($category)
    {
        $pcqp = array('query' => '', 'params' => array());

        if (null === $category) {
            $pcqp['query'] = 'p.category IS NULL';
        } else {
            $pcqp['query'] = 'c.slug = :category';
            $pcqp['params'] = array('category' => $category);
        }

        return $pcqp;
    }
}
