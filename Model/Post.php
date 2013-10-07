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
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\Tag;

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

    protected $commentsCount = 0;

    protected $author;

    protected $image;

    protected $collection;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setPublicationDateStart(new \DateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->setSlug(Tag::slugify($title));
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * {@inheritdoc}
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublicationDateStart(\DateTime $publicationDateStart = null)
    {
        $this->publicationDateStart = $publicationDateStart;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicationDateStart()
    {
        return $this->publicationDateStart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function addComments(CommentInterface $comment)
    {
        $this->comments[] = $comment;
        $comment->setPost($this);
    }

    /**
     * {@inheritdoc}
     */
    public function setComments($comments)
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection;

        foreach ($this->comments as $comment) {
            $this->addComments($comment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * {@inheritdoc}
     */
    public function addTags(TagInterface $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getYear()
    {
        return $this->getPublicationDateStart()->format('Y');
    }

    /**
     * {@inheritdoc}
     */
    public function getMonth()
    {
        return $this->getPublicationDateStart()->format('m');
    }

    /**
     * {@inheritdoc}
     */
    public function getDay()
    {
        return $this->getPublicationDateStart()->format('d');
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->commentsEnabled = $commentsEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsCloseAt(\DateTime $commentsCloseAt = null)
    {
        $this->commentsCloseAt = $commentsCloseAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsCloseAt()
    {
        return $this->commentsCloseAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsDefaultStatus($commentsDefaultStatus)
    {
        $this->commentsDefaultStatus = $commentsDefaultStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsDefaultStatus()
    {
        return $this->commentsDefaultStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function isPublic()
    {
        if (!$this->getEnabled()) {
            return false;
        }

        return $this->getPublicationDateStart()->diff(new \DateTime)->invert == 0 ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection(CollectionInterface $collection = null)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param $contentFormatter
     */
    public function setContentFormatter($contentFormatter)
    {
        $this->contentFormatter = $contentFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentFormatter()
    {
        return $this->contentFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawContent($rawContent)
    {
        $this->rawContent = $rawContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }
}
