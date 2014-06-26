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

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\ClassificationBundle\Model\CollectionInterface;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;

class PostManager extends BaseEntityManager implements PostManagerInterface
{

    /**
     * @param string        $permalink
     * @param BlogInterface $blog
     *
     * @return PostInterface
     */
    public function findOneByPermalink($permalink, BlogInterface $blog)
    {
        $repository = $this->getRepository();

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

        if (isset($urlParameters['collection'])) {
            $pcqp = $this->getPublicationCollectionQueryParts($urlParameters['collection']);

            $parameters = array_merge($parameters, $pcqp['params']);

            $query
                ->leftJoin('p.collection', 'c')
                ->andWhere($pcqp['query'])
            ;
        }

        if (count($parameters) == 0) {
            return null;
        }

        $query->setParameters($parameters);

        $results = $query->getQuery()->getResult();

        if (count($results) > 0) {
            return $results[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * Valid criteria are:
     *    enabled - boolean
     *    date - query
     *    tag - string
     *    author - 'NULL', 'NOT NULL', id, array of ids
     *    collections - CollectionInterface
     *    mode - string public|admin
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }

        $parameters = array();
        $query = $this->getRepository()
            ->createQueryBuilder('p')
            ->select('p, t')
            ->orderBy('p.publicationDateStart', 'DESC');

        if ($criteria['mode'] == 'admin') {
            $query
                ->leftJoin('p.tags', 't')
                ->leftJoin('p.author', 'a')
            ;
        } else {
            $query
                ->leftJoin('p.tags', 't', Join::WITH, 't.enabled = true')
                ->leftJoin('p.author', 'a', Join::WITH, 'a.enabled = true')
            ;
        }

        if (!isset($criteria['enabled']) && $criteria['mode'] == 'public') {
            $criteria['enabled'] = true;
        }
        if (isset($criteria['enabled'])) {
            $query->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        if (isset($criteria['date']) && isset($criteria['date']['query']) && isset($criteria['date']['params'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

        if (isset($criteria['tag'])) {
            $query->andWhere('t.slug LIKE :tag');
            $parameters['tag'] = (string) $criteria['tag'];
        }

        if (isset($criteria['author'])) {
            if (!is_array($criteria['author']) && stristr($criteria['author'], 'NULL')) {
                $query->andWhere('p.author IS ' . $criteria['author']);
            } else {
                $query->andWhere(sprintf('p.author IN (%s)', implode((array) $criteria['author'], ',')));
            }
        }

        if (isset($criteria['collection']) && $criteria['collection'] instanceof CollectionInterface) {
            $query->andWhere('p.collection = :collectionid');
            $parameters['collectionid'] = $criteria['collection']->getId();
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
     * @param string $date  Date in format YYYY-MM-DD
     * @param string $step  Interval step: year|month|day
     * @param string $alias Table alias for the publicationDateStart column
     *
     * @return array
     */
    protected function getPublicationDateQueryParts($date, $step, $alias = 'p')
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
     * @param string $collection
     *
     * @return array
     */
    protected function getPublicationCollectionQueryParts($collection)
    {
        $pcqp = array('query' => '', 'params' => array());

        if (null === $collection) {
            $pcqp['query'] = 'p.collection IS NULL';
        } else {
            $pcqp['query'] = 'c.slug = :collection';
            $pcqp['params'] = array('collection' => $collection);
        }

        return $pcqp;
    }
}
