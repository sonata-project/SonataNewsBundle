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

namespace Sonata\NewsBundle\Entity;

use Doctrine\ORM\Query\Expr\Join;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;

class PostManager extends BaseEntityManager implements PostManagerInterface
{
    /**
     * @param string $permalink
     *
     * @return PostInterface|null
     */
    public function findOneByPermalink($permalink, BlogInterface $blog)
    {
        $query = $this->getRepository()->createQueryBuilder('p');

        try {
            $urlParameters = $blog->getPermalinkGenerator()->getParameters($permalink);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        $parameters = [];

        if (isset($urlParameters['year'], $urlParameters['month'], $urlParameters['day'])) {
            $dateQueryParts = $this->getPublicationDateQueryParts(
                sprintf('%d-%d-%d', $urlParameters['year'], $urlParameters['month'], $urlParameters['day']),
                'day'
            );

            $parameters = $dateQueryParts['params'];

            $query->andWhere($dateQueryParts['query']);
        }

        if (isset($urlParameters['slug'])) {
            $query->andWhere('p.slug = :slug');
            $parameters['slug'] = $urlParameters['slug'];
        }

        if (isset($urlParameters['collection'])) {
            $collectionQueryParts = $this->getPublicationCollectionQueryParts($urlParameters['collection']);

            $parameters = array_merge($parameters, $collectionQueryParts['params']);

            $query
                ->leftJoin('p.collection', 'c')
                ->andWhere($collectionQueryParts['query']);
        }

        if (0 === \count($parameters)) {
            return null;
        }

        $query->setParameters($parameters);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Valid criteria are:
     *    enabled - boolean
     *    date - query
     *    tag - string
     *    author - 'NULL', 'NOT NULL', id, array of ids
     *    collections - CollectionInterface
     *    mode - string public|admin.
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }

        $parameters = [];
        $query = $this->getRepository()
            ->createQueryBuilder('p')
            ->select('p, t')
            ->orderBy('p.publicationDateStart', 'DESC');

        if ('admin' === $criteria['mode']) {
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

        if (!isset($criteria['enabled']) && 'public' === $criteria['mode']) {
            $criteria['enabled'] = true;
        }
        if (isset($criteria['enabled'])) {
            $query->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        if (isset($criteria['date'], $criteria['date']['query'], $criteria['date']['params'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

        if (isset($criteria['tag'])) {
            $query
                ->leftJoin('p.tags', 't2')
                ->andWhere('t2.slug LIKE :tag');
            $parameters['tag'] = (string) $criteria['tag'];
        }

        if (isset($criteria['author'])) {
            if (!\is_array($criteria['author']) && stristr($criteria['author'], 'NULL')) {
                $query->andWhere('p.author IS '.$criteria['author']);
            } else {
                $query->andWhere(sprintf('p.author IN (%s)', implode(',', (array) $criteria['author'])));
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
    public function getPublicationDateQueryParts($date, $step, $alias = 'p')
    {
        return [
            'query' => sprintf('%s.publicationDateStart >= :startDate AND %s.publicationDateStart < :endDate', $alias, $alias),
            'params' => [
                'startDate' => new \DateTime($date),
                'endDate' => new \DateTime($date.'+1 '.$step),
            ],
        ];
    }

    /**
     * @param string $collection
     *
     * @return array
     */
    protected function getPublicationCollectionQueryParts($collection)
    {
        $queryParts = ['query' => '', 'params' => []];

        if (null === $collection) {
            $queryParts['query'] = 'p.collection IS NULL';
        } else {
            $queryParts['query'] = 'c.slug = :collection';
            $queryParts['params'] = ['collection' => $collection];
        }

        return $queryParts;
    }
}
