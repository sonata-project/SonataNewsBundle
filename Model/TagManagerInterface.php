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

interface TagManagerInterface
{
    /**
     * Creates an empty media instance
     *
     * @return Post
     */
    function create();

    /**
     * Deletes a post
     *
     * @param TagInterface $comment
     * @return void
     */
    function delete(TagInterface $comment);

    /**
     * Finds one post by the given criteria
     *
     * @param array $criteria
     * @return TagInterface
     */
    function findOneBy(array $criteria);

    /**
     * Finds one post by the given criteria
     *
     * @param array $criteria
     * @return TagInterface
     */
    function findBy(array $criteria);

    /**
     * Returns the post's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Save a post
     *
     * @param TagInterface $comment
     * @return void
     */
    function save(TagInterface $comment);
}