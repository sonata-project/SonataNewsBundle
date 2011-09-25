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

use Sonata\NewsBundle\Model\PostManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;

use Sonata\AdminBundle\Datagrid\ORM\Pager;
use Sonata\AdminBundle\Datagrid\ORM\ProxyQuery;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;

class PostManager implements PostManagerInterface
{
    protected $em;
    protected $class;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * @param \Sonata\NewsBundle\Model\PostInterface $post
     * @return void
     */
    public function save(PostInterface $post)
    {
        $this->em->persist($post);
        $this->em->flush();
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
        return $this->em->getRepository($this->class)->findOneBy($criteria);
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
        try {
            return $this->em->getRepository($this->class)
                ->createQueryBuilder('p')
                ->where('p.publicationDateStart LIKE :publicationDateStart AND p.slug = :slug')
                ->setParameters(array(
                    'publicationDateStart' => sprintf('%s-%s-%s%%', $year, $month, $day),
                    'slug' => $slug
                ))
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findBy($criteria);
    }

    /**
     * @param \Sonata\NewsBundle\Model\PostInterface $post
     * @return void
     */
    public function delete(PostInterface $post)
    {
        $this->em->remove($post);
        $this->em->flush();
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
        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('p')
            ->select('p, t')
            ->leftJoin('p.tags', 't')
            ->orderby('p.publicationDateStart', 'DESC');

        // enabled
        $criteria['enabled'] = isset($criteria['enabled']) ? $criteria['enabled'] : true;
        $query->andWhere('p.enabled = :enabled');
        $parameters['enabled'] = $criteria['enabled'];

        if (isset($criteria['date'])) {
            $query->andWhere('p.publicationDateStart LIKE :date');
            $parameters['date'] = $criteria['date'];
        }

        if (isset($criteria['tag'])) {
            $query->andWhere('t.slug LIKE :tag and t.enabled = :tag_enabled');
            $parameters['tag'] = $criteria['tag'];
            $parameters['tag_enabled'] = true;
        }

        $query->setParameters($parameters);

        $pager = new Pager;
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}