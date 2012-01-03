<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\TagInterface;
use Sonata\NewsBundle\Model\CategoryInterface;

abstract class Post implements PostInterface
{
    protected $title;

    protected $slug;

    protected $abstract;

    protected $content;

    protected $rawContent;

    protected $contentFormatter;

    protected $tags;

    protected $comments;

    protected $enabled;

    protected $publicationDateStart;

    protected $createdAt;

    protected $updatedAt;

    protected $commentsEnabled = true;

    protected $commentsCloseAt;

    protected $commentsDefaultStatus;

    protected $author;

    protected $image;

    protected $category;

    public function __construct()
    {
        $this->setPublicationDateStart(new \DateTime);
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->setSlug(Tag::slugify($title));
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
     * @param \DateTime $publicationDateStart
     */
    public function setPublicationDateStart(\DateTime $publicationDateStart = null)
    {
        $this->publicationDateStart = $publicationDateStart;
    }

    /**
     * Get publication_date_start
     *
     * @return \DateTime $publicationDateStart
     */
    public function getPublicationDateStart()
    {
        return $this->publicationDateStart;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add comments
     *
     * @param \Sonata\NewsBundle\Model\CommentInterface $comments
     */
    public function addComments(CommentInterface $comment)
    {
        $this->comments[] = $comment;
        $comment->setPost($this);
    }

    public function setComments($comments)
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection;

        foreach ($this->comments as $comment) {
            $this->addComments($comment);
        }
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
     * @param \Sonata\NewsBundle\Model\TagInterface $tags
     */
    public function addTags(TagInterface $tags)
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
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new \DateTime);
        }

        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    public function getYear()
    {
        return $this->getPublicationDateStart()->format('Y');
    }

    public function getMonth()
    {
        return $this->getPublicationDateStart()->format('m');
    }

    public function getDay()
    {
        return $this->getPublicationDateStart()->format('d');
    }

    /**
     * Set comments_enabled
     *
     * @param boolean $commentsEnabled
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->commentsEnabled = $commentsEnabled;
    }

    /**
     * Get comments_enabled
     *
     * @return boolean $commentsEnabled
     */
    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }

    /**
     * Set comments_close_at
     *
     * @param \DateTime|null $commentsCloseAt
     */
    public function setCommentsCloseAt(\DateTime $commentsCloseAt = null)
    {
        $this->commentsCloseAt = $commentsCloseAt;
    }

    /**
     * Get comments_close_at
     *
     * @return datetime $commentsCloseAt
     */
    public function getCommentsCloseAt()
    {
        return $this->commentsCloseAt;
    }

    /**
     * Set comments_default_status
     *
     * @param integer $commentsDefaultStatus
     */
    public function setCommentsDefaultStatus($commentsDefaultStatus)
    {
        $this->commentsDefaultStatus = $commentsDefaultStatus;
    }

    /**
     * Get comments_default_status
     *
     * @return integer $commentsDefaultStatus
     */
    public function getCommentsDefaultStatus()
    {
        return $this->commentsDefaultStatus;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return bool
     */
    public function isCommentable()
    {
        if (!$this->getCommentsEnabled() || !$this->getEnabled()) {
            return false;
        }

        if ($this->getCommentsCloseAt() instanceof \DateTime) {
            return $this->getCommentsCloseAt()->diff(new \DateTime)->invert == 1 ? true : false;
        }

        return true;
    }

    public function isPublic()
    {
        if (!$this->getEnabled() || $this->getCategory() && !$this->getCategory()->getEnabled()) {
            return false;
        }

        return $this->getPublicationDateStart()->diff(new \DateTime)->invert == 0 ? true : false;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param CategoryInterface $category
     * @return void
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setContentFormatter($contentFormatter)
    {
        $this->contentFormatter = $contentFormatter;
    }

    public function getContentFormatter()
    {
        return $this->contentFormatter;
    }

    public function setRawContent($rawContent)
    {
        $this->rawContent = $rawContent;
    }

    public function getRawContent()
    {
        return $this->rawContent;
    }
}
