<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\Sonata\NewsBundle\Entity;

abstract class BasePost
{

    protected $title;

    protected $slug;

    protected $abstract;

    protected $content;

    protected $tags;

    protected $comments;

    protected $enabled;

    protected $publication_date_start;

    protected $created_at;

    protected $updated_at;

    protected $comments_enabled = true;

    protected $comments_close_at;

    protected $comments_default_status;

    public function __construct()
    {
        $this->tags     = new \Doctrine\Common\Collections\ArrayCollection;
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->setSlug(BaseTag::slugify($title));
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set abstract
     *
     * @param text $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * Get abstract
     *
     * @return text $abstract
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set slug
     *
     * @param integer $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return integer $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set publication_date_start
     *
     * @param datetime $publicationDateStart
     */
    public function setPublicationDateStart($publicationDateStart)
    {
        $this->publication_date_start = $publicationDateStart;
    }

    /**
     * Get publication_date_start
     *
     * @return datetime $publicationDateStart
     */
    public function getPublicationDateStart()
    {
        return $this->publication_date_start;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add comments
     *
     * @param Application\Sonata\NewsBundle\Entity\Comment $comments
     */
    public function addComments(\Application\Sonata\NewsBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add tags
     *
     * @param Application\Sonata\NewsBundle\Entity\Tag $tags
     */
    public function addTags(\Application\Sonata\NewsBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    public function getYear()
    {
        return $this->getCreatedAt()->format('Y');
    }

    public function getMonth()
    {
        return $this->getCreatedAt()->format('m');
    }

    public function getDay()
    {
        return $this->getCreatedAt()->format('d');
    }
    /**
     * Set comments_enabled
     *
     * @param boolean $commentsEnabled
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->comments_enabled = $commentsEnabled;
    }

    /**
     * Get comments_enabled
     *
     * @return boolean $commentsEnabled
     */
    public function getCommentsEnabled()
    {
        return $this->comments_enabled;
    }

    /**
     * Set comments_close_at
     *
     * @param datetime $commentsCloseAt
     */
    public function setCommentsCloseAt($commentsCloseAt)
    {
        $this->comments_close_at = $commentsCloseAt;
    }

    /**
     * Get comments_close_at
     *
     * @return datetime $commentsCloseAt
     */
    public function getCommentsCloseAt()
    {
        return $this->comments_close_at;
    }

    /**
     * Set comments_default_status
     *
     * @param integer $commentsDefaultStatus
     */
    public function setCommentsDefaultStatus($commentsDefaultStatus)
    {
        $this->comments_default_status = $commentsDefaultStatus;
    }

    /**
     * Get comments_default_status
     *
     * @return integer $commentsDefaultStatus
     */
    public function getCommentsDefaultStatus()
    {
        return $this->comments_default_status;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function isCommentable()
    {

        if(!$this->getCommentsEnabled())
        {
            return false;
        }

        if($this->getCommentsCloseAt() instanceof \DateTime)
        {
            return $this->getCommentsCloseAt()->diff(new \DateTime)->invert == 0 ? true : false;
        }

        return $this->getEnabled();
    }
}