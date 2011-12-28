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

class CategoryPermalink implements PermalinkInterface
{
    /**
     * @param Sonata\NewsBundle\Model\PostInterface $post
     * 
     * @return string
     */
    public function generate(PostInterface $post)
    {
        return null == $post->getCategory()
            ? $post->getSlug()
            : sprintf('%s/%s', $post->getCategory()->getSlug(), $post->getSlug());
    }
    
    /**
     * @param string $permalink
     * @param Doctrine\Common\Persistence\ObjectRepository $repository
     * 
     * @return Doctrine\Common\Persistence\ObjectRepository
     */
    public function processRepository($permalink, ObjectRepository $repository)
    {
        if (false === strpos($permalink, '/')) {
            $category = null;
            $slug = $permalink;
        } else {
            list($category, $slug) = explode('/', $permalink);
        }
        
        $pcqp = $this->getPublicationCategoryQueryParts($category);
        
        return $repository
            ->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->where('p.slug = :slug')
            ->andWhere($pcqp['query'])
            ->setParameters(array_merge($pcqp['params'], array('slug' => $slug)));
    }
    
    /**
     * @param string $category
     * 
     * @return array
     */
    protected function getPublicationCategoryQueryParts($category)
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
