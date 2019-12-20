<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\Tag;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Post implements PostInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $abstract;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $rawContent;

    /**
     * @var string
     */
    protected $contentFormatter;

    /**
     * @var Collection|TagInterface[]
     */
    protected $tags;

    /**
     * @var Collection|CommentInterface[]
     */
    protected $comments;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var \DateTime|null
     */
    protected $publicationDateStart;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var bool
     */
    protected $commentsEnabled = true;

    /**
     * @var \DateTime|null
     */
    protected $commentsCloseAt;

    /**
     * @var int
     */
    protected $commentsDefaultStatus;

    /**
     * @var int
     */
    protected $commentsCount = 0;

    /**
     * @var UserInterface|null
     */
    protected $author;

    /**
     * @var MediaInterface|null
     */
    protected $image;

    /**
     * @var CollectionInterface|null
     */
    protected $collection;

    public function __construct()
    {
        $this->setPublicationDateStart(new \DateTime());
    }

    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    public function setTitle($title): void
    {
        $this->title = $title;

        $this->setSlug(Tag::slugify($title));
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setAbstract($abstract): void
    {
        $this->abstract = $abstract;
    }

    public function getAbstract()
    {
        return $this->abstract;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setPublicationDateStart(\DateTime $publicationDateStart = null): void
    {
        $this->publicationDateStart = $publicationDateStart;
    }

    public function getPublicationDateStart()
    {
        return $this->publicationDateStart;
    }

    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function addComments(CommentInterface $comment): void
    {
        $this->comments[] = $comment;
        $comment->setPost($this);
    }

    public function setComments($comments): void
    {
        $this->comments = new ArrayCollection();

        foreach ($this->comments as $comment) {
            $this->addComments($comment);
        }
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function addTags(TagInterface $tags): void
    {
        $this->tags[] = $tags;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags): void
    {
        $this->tags = $tags;
    }

    public function prePersist(): void
    {
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new \DateTime());
        }

        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function preUpdate(): void
    {
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new \DateTime());
        }

        $this->setUpdatedAt(new \DateTime());
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

    public function setCommentsEnabled($commentsEnabled): void
    {
        $this->commentsEnabled = $commentsEnabled;
    }

    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }

    public function setCommentsCloseAt(\DateTime $commentsCloseAt = null): void
    {
        $this->commentsCloseAt = $commentsCloseAt;
    }

    public function getCommentsCloseAt()
    {
        return $this->commentsCloseAt;
    }

    public function setCommentsDefaultStatus($commentsDefaultStatus): void
    {
        $this->commentsDefaultStatus = $commentsDefaultStatus;
    }

    public function getCommentsDefaultStatus()
    {
        return $this->commentsDefaultStatus;
    }

    public function setCommentsCount($commentsCount): void
    {
        $this->commentsCount = $commentsCount;
    }

    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    public function isCommentable()
    {
        if (!$this->getCommentsEnabled() || !$this->getEnabled()) {
            return false;
        }

        if ($this->getCommentsCloseAt() instanceof \DateTime) {
            return 1 === $this->getCommentsCloseAt()->diff(new \DateTime())->invert ? true : false;
        }

        return true;
    }

    public function isPublic()
    {
        if (!$this->getEnabled()) {
            return false;
        }

        return 0 === $this->getPublicationDateStart()->diff(new \DateTime())->invert ? true : false;
    }

    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setCollection(CollectionInterface $collection = null): void
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param $contentFormatter
     */
    public function setContentFormatter($contentFormatter): void
    {
        $this->contentFormatter = $contentFormatter;
    }

    public function getContentFormatter()
    {
        return $this->contentFormatter;
    }

    public function setRawContent($rawContent): void
    {
        $this->rawContent = $rawContent;
    }

    public function getRawContent()
    {
        return $this->rawContent;
    }
}
