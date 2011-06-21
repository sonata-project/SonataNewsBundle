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

interface CommentManagerInterface
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
     * @param CommentInterface $comment
     * @return void
     */
    function delete(CommentInterface $comment);

    /**
     * Finds one post by the given criteria
     *
     * @param array $criteria
     * @return CommentInterface
     */
    function findOneBy(array $criteria);

    /**
     * Finds one post by the given criteria
     *
     * @param array $criteria
     * @return CommentInterface
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
     * @param CommentInterface $comment
     * @return void
     */
    function save(CommentInterface $comment);
}