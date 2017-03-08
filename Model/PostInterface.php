<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\ClassificationBundle\Model\TagInterface;
use Sonata\MediaBundle\Model\MediaInterface;

interface PostInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * Set title.
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Get title.
     *
     * @return string $title
     */
    public function getTitle();

    /**
     * Set abstract.
     *
     * @param string $abstract
     */
    public function setAbstract($abstract);

    /**
     * Get abstract.
     *
     * @return string $abstract
     */
    public function getAbstract();

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent($content);

    /**
     * Get content.
     *
     * @return string $content
     */
    public function getContent();

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * Set slug.
     *
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * Get slug.
     *
     * @return string $slug
     */
    public function getSlug();

    /**
     * Set publication_date_start.
     *
     * @param \DateTime $publicationDateStart
     */
    public function setPublicationDateStart(\DateTime $publicationDateStart = null);

    /**
     * Get publication_date_start.
     *
     * @return \DateTime $publicationDateStart
     */
    public function getPublicationDateStart();

    /**
     * Set created_at.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get created_at.
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt();

    /**
     * Set updated_at.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updated_at.
     *
     * @return \Datetime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Add comments.
     *
     * @param CommentInterface $comments
     */
    public function addComments(CommentInterface $comments);

    /**
     * @param Collection|CommentInterface[] $comments
     */
    public function setComments($comments);

    /**
     * Get comments.
     *
     * @return Collection|CommentInterface[] $comments
     */
    public function getComments();

    /**
     * Add tags.
     *
     * @param TagInterface $tags
     */
    public function addTags(TagInterface $tags);

    /**
     * Get tags.
     *
     * @return Collection|TagInterface[] $tags
     */
    public function getTags();

    /**
     * @param Collection|TagInterface[]$tags
     */
    public function setTags($tags);

    /**
     * @return string
     */
    public function getYear();

    /**
     * @return string
     */
    public function getMonth();

    /**
     * @return string
     */
    public function getDay();

    /**
     * Set comments_enabled.
     *
     * @param bool $commentsEnabled
     */
    public function setCommentsEnabled($commentsEnabled);

    /**
     * Get comments_enabled.
     *
     * @return bool $commentsEnabled
     */
    public function getCommentsEnabled();

    /**
     * Set comments_close_at.
     *
     * @param \DateTime $commentsCloseAt
     */
    public function setCommentsCloseAt(\DateTime $commentsCloseAt = null);

    /**
     * Get comments_close_at.
     *
     * @return \DateTime $commentsCloseAt
     */
    public function getCommentsCloseAt();

    /**
     * Set comments_default_status.
     *
     * @param int $commentsDefaultStatus
     */
    public function setCommentsDefaultStatus($commentsDefaultStatus);

    /**
     * Get comments_default_status.
     *
     * @return int $commentsDefaultStatus
     */
    public function getCommentsDefaultStatus();

    /**
     * Set comments_count.
     *
     * @param int $commentscount
     */
    public function setCommentsCount($commentscount);

    /**
     * Get comments_count.
     *
     * @return int $commentsCount
     */
    public function getCommentsCount();

    /**
     * @return bool
     */
    public function isCommentable();

    /**
     * @return bool
     */
    public function isPublic();

    /**
     * @param UserInterface $author
     */
    public function setAuthor($author);

    /**
     * @return UserInterface
     */
    public function getAuthor();

    /**
     * @param MediaInterface $image
     */
    public function setImage($image);

    /**
     * @return MediaInterface
     */
    public function getImage();

    /**
     * @return CollectionInterface
     */
    public function getCollection();

    /**
     * @param CollectionInterface $collection
     */
    public function setCollection(CollectionInterface $collection = null);

    /**
     * @param string $contentFormatter
     */
    public function setContentFormatter($contentFormatter);

    /**
     * @return string
     */
    public function getContentFormatter();

    /**
     * @param string $rawContent
     */
    public function setRawContent($rawContent);

    /**
     * @return string
     */
    public function getRawContent();
}
