<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Permalink;

use Sonata\NewsBundle\Model\PostInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class DatePermalink implements PermalinkInterface
{
    /**
     * @param Sonata\NewsBundle\Model\PostInterface $post
     * 
     * @return string
     */
    public function generate(PostInterface $post)
    {
        return sprintf('%d/%d/%d/%s', 
            $post->getYear(), 
            $post->getMonth(), 
            $post->getDay(), 
            $post->getSlug());
    }
    
    /**
     * @param string $permalink
     * @param Doctrine\Common\Persistence\ObjectRepository $repository
     * 
     * @return Doctrine\Common\Persistence\ObjectRepository 
     */
    public function processRepository($permalink, ObjectRepository $repository)
    {
        list($year, $month, $day, $slug) = explode('/', $permalink);
        
        $pdqp = $this->getPublicationDateQueryParts(sprintf('%s-%s-%s', $year, $month, $day), 'day');
        
        return $repository
            ->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere($pdqp['query'])
            ->setParameters(array_merge($pdqp['params'], array('slug' => $slug)));
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
}
