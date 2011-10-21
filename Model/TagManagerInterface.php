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
    function create();

    /**
     * Deletes a post
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    function delete(TagInterface $tag);

    /**
     * Finds one tag by the given criteria
     *
     * @param array $criteria
     *
     * @return TagInterface
     */
    function findOneBy(array $criteria);

    /**
     * Finds one tag by the given criteria
     *
     * @param array $criteria
     *
     * @return TagInterface
     */
    function findBy(array $criteria);

    /**
     * Returns the tag's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Save a tag
     *
     * @param TagInterface $tag
     *
     * @return void
     */
    function save(TagInterface $tag);
}