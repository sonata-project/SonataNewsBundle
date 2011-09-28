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

use Sonata\NewsBundle\Model\PostManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;

use Sonata\AdminBundle\Datagrid\ORM\Pager;
use Sonata\AdminBundle\Datagrid\ORM\ProxyQuery;

use Doctrine\ODM\MongoDB\DocumentManager;

class PostManager implements PostManagerInterface
{
    protected $dm;
    protected $class;

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @param $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm    = $dm;
        $this->class = $class;
    }

    /**
     * @param \Sonata\NewsBundle\Model\PostInterface $post
     * @return void
     */
    public function save(PostInterface $post)
    {
        $this->dm->persist($post);
        $this->dm->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param array $criteria
     * @return \Sonata\NewsBundle\Model\PostInterface|null
     */
    public function findOneBy(array $criteria)
    {
        return $this->dm->getRepository($this->class)->findOneBy($criteria);
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $slug
     * @return \Sonata\NewsBundle\Model\PostInterface|null
     */
    public function findOneBySlug($year, $month, $day, $slug)
    {
        $pdqp = $this->getPublicationDateQueryParts(sprintf('%s-%s-%s', $year, $month, $day), 'day');
        return $this->dm->getRepository($this->class)
            ->createQueryBuilder()
            ->field('slug')->equals($slug)

            ->andWhere($pdqp['query'])
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        return $this->dm->getRepository($this->class)->findBy($criteria);
    }

    /**
     * @param \Sonata\NewsBundle\Model\PostInterface $post
     * @return void
     */
    public function delete(PostInterface $post)
    {
        $this->dm->remove($post);
        $this->dm->flush();
    }

    /**
     * @return \Sonata\NewsBundle\Model\PostInterface
     */
    public function create()
    {
        return new $this->class;
    }

    /**
     * @param array $criteria
     * @param $page
     * @return \Sonata\AdminBundle\Datagrid\ORM\Pager
     */
    public function getPager(array $criteria, $page)
    {
        $parameters = array();
        $query = $this->dm->getRepository($this->class)
            ->createQueryBuilder('p')
            ->select('p, t')
            ->leftJoin('p.tags', 't')
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
            $query->andWhere('t.slug LIKE :tag and t.enabled = :tag_enabled');
            $parameters['tag'] = $criteria['tag'];
            $parameters['tag_enabled'] = true;
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
