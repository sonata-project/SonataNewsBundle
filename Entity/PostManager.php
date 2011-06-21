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

use Doctrine\ORM\EntityManager;

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

    public function findBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findBy($criteria);
    }

    public function delete(PostInterface $post)
    {
        $this->em->remove($post);
        $this->em->flush();
    }

    function create()
    {
        return new $this->class;
    }
}