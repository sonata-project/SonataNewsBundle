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

interface TagInterface
{
    /**
     * @param $name
     *
     * @return mixed
     */
    function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    function getName();

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    function getEnabled();

    /**
     * Set slug
     *
     * @param integer $slug
     */
    function setSlug($slug);

    /**
     * Get slug
     *
     * @return integer $slug
     */
    function getSlug();

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
     * @return \DateTime $updatedAt
     */
    function getUpdatedAt();

    /**
     * Add posts
     *
     * @param \Sonata\NewsBundle\Model\PostInterface $posts
     */
    function addPosts(PostInterface $posts);

    /**
     * Get posts
     *
     * @return array $posts
     */
    function getPosts();
}