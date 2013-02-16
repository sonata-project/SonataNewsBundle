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
     * Creates an empty tag instance
     *
     * @return Tag
     */
    public function create();

    /**
     * Deletes a post
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    public function delete(TagInterface $tag);

    /**
     * Finds one tag by the given criteria
     *
     * @param array $criteria
     *
     * @return TagInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds one tag by the given criteria
     *
     * @param array $criteria
     *
     * @return TagInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the tag's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Save a tag
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    public function save(TagInterface $tag);
}
