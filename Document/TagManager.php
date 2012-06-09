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

use Sonata\NewsBundle\Model\TagManager as ModelTagManager;
use Sonata\NewsBundle\Model\TagInterface;

use Doctrine\ORM\DocumentManager;

class TagManager extends ModelTagManager
{
    /**
     * @var \Doctrine\ORM\DocumentManager;
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\DocumentManager $em
     * @param string                        $class
     */
    public function __construct(DocumentManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function save(TagInterface $tag)
    {
        $this->em->persist($tag);
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
     * {@inheritDoc}
     */
    public function findBy(array $criteria)
    {
        return $this->em->getRepository($this->class)->findBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(TagInterface $tag)
    {
        $this->em->remove($tag);
        $this->em->flush();
    }
}
