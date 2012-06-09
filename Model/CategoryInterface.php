<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Sonata Project <https://github.com/sonata-project/SonataNewsBundle/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

interface CategoryInterface
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
     * Set description
     *
     * @param string $description
     */
    function setDescription($description);

    /**
     * Get description
     *
     * @return string $description
     */
    function getDescription();

    /**
     * Set count
     *
     * @param integer $count
     */
    function setCount($count);

    /**
     * Get count
     *
     * @return integer $count
     */
    function getCount();

    /**
     * Add posts
     *
     * @param \Sonata\NewsBundle\Model\PostInterface $posts
     */
    function addPosts(PostInterface $posts);

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection $posts
     */
    function getPosts();
}