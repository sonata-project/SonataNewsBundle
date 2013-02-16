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

interface CommentInterface
{
    const STATUS_INVALID  = 0;
    const STATUS_VALID    = 1;
    const STATUS_MODERATE = 2;

    public function getId();

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail();

    /**
     * Set url
     *
     * @param text $url
     */
    public function setUrl($url);

    /**
     * Get url
     *
     * @return text $url
     */
    public function getUrl();

    /**
     * Set message
     *
     * @param text $message
     */
    public function setMessage($message);

    /**
     * Get message
     *
     * @return text $message
     */
    public function getMessage();

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get created_at
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt();

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt();

    public function getStatusCode();

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus();

    /**
     * Set post
     *
     * @param \Sonata\NewsBundle\Model\PostInterface $post
     */
    public function setPost($post);

    /**
     * Get post
     *
     * @return \Sonata\NewsBundle\Model\PostInterface $post
     */
    public function getPost();
}
