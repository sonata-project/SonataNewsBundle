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

    function getId();

    /**
     * Set name
     *
     * @param string $name
     */
    function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    function getName();

    /**
     * Set email
     *
     * @param string $email
     */
    function setEmail($email);

    /**
     * Get email
     *
     * @return string $email
     */
    function getEmail();

    /**
     * Set url
     *
     * @param text $url
     */
    function setUrl($url);

    /**
     * Get url
     *
     * @return text $url
     */
    function getUrl();

    /**
     * Set message
     *
     * @param text $message
     */
    function setMessage($message);

    /**
     * Get message
     *
     * @return text $message
     */
    function getMessage();

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get created_at
     *
     * @return \DateTime $createdAt
     */
    function getCreatedAt();

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    function getUpdatedAt();

    function getStatusCode();

    /**
     * Set status
     *
     * @param integer $status
     */
    function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    function getStatus();

    /**
     * Set post
     *
     * @param \Sonata\NewsBundle\Model\PostInterface $post
     */
    function setPost($post);

    /**
     * Get post
     *
     * @return \Sonata\NewsBundle\Model\PostInterface $post
     */
    function getPost();
}