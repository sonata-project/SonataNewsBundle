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

use Sonata\NewsBundle\Model\TagManager as ModelTagManager;
use Sonata\NewsBundle\Model\TagInterface;

use Doctrine\ORM\EntityManager;

class TagManager extends ModelTagManager
{
    protected $em;

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
     * @param \Sonata\NewsBundle\Model\TagInterface $tag
     * @return void
     */
    public function save(TagInterface $tag)
    {
        $this->em->persist($tag);
        $this->em->flush();
    }

    /**
     * @param array $criteria
     * @return object
     */
    public function findOneBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findOneBy($criteria);
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
     * @param \Sonata\NewsBundle\Model\TagInterface $tag
     * @return void
     */
    public function delete(TagInterface $tag)
    {
        $this->em->remove($tag);
        $this->em->flush();
    }
}