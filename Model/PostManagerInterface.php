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

interface PostManagerInterface
{
    /**
     * Creates an empty post instance
     *
     * @return Post
     */
    public function create();

    /**
     * Deletes a post
     *
     * @param PostInterface $post
     *
     * @return void
     */
    public function delete(PostInterface $post);

    /**
     * Finds one post by the given criteria
     *
     * @param array $criteria
     *
     * @return PostInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds one post by the given criteria
     *
     * @param array $criteria
     *
     * @return PostInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the post's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Save a post
     *
     * @param PostInterface $post
     *
     * @return void
     */
    public function save(PostInterface $post);
}
