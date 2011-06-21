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

    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    public function save(PostInterface $post)
    {
        $this->em->persist($post);
        $this->em->flush();
    }

    public function getClass()
    {
        return $this->class;
    }

    public function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
    }

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

    public function findBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findBy($criteria);
    }

    public function delete(PostInterface $post)
    {
        $this->em->remove($post);
        $this->em->flush();
    }

    public function create()
    {
        return new $this->class;
    }

    public function getPager(array $criteria, $page)
    {
        $parameters = array();
        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('p')
            ->orderby('p.createdAt', 'DESC');

        $criteria['enabled'] = isset($criteria['enabled']) ? $criteria['enabled'] : true;
        $query->andWhere('p.enabled = :enabled');
        $parameters['enabled'] = $criteria['enabled'];

        $query->setParameters($parameters);

        $pager = new Pager;
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}