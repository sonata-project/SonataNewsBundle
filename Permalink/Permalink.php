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
use Sonata\NewsBundle\Model\BlogInterface;

class Permalink implements PermalinkInterface
{
    /**
     * @var BlogInteface
     */
    protected $blog;
    
    /**
     * @param Sonata\NewsBundle\Model\BlogInterface $blog
     */
    public function __construct(BlogInterface $blog)
    {
        $this->blog = $blog;
    }
    
    /**
     * @param Sonata\NewsBundle\Model\PostInterface $post
     * @return string
     */
    public function generate(PostInterface $post)
    {
        if ('date' === $this->blog->getRoutingMethod()) {
            $permalink = sprintf('%d/%d/%d/%s', 
                $post->getYear(), 
                $post->getMonth(), 
                $post->getDay(), 
                $post->getSlug());
        } elseif ('category' === $this->blog->getRoutingMethod()) {
            $permalink = null == $post->getCategory()
                ? $post->getSlug()
                : sprintf('%s/%s', $post->getCategory()->getSlug(), $post->getSlug());
        } else {
             throw new \Exception('The routing method has an invalid value');
        }
        
        return $permalink;
    }
}
