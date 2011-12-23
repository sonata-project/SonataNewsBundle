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

interface CategoryManagerInterface
{
    /**
     * Creates an empty category instance
     *
     * @return Category
     */
    function create();

    /**
     * Deletes a post
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    function delete(CategoryInterface $category);

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     *
     * @return CategoryInterface
     */
    function findOneBy(array $criteria);

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     *
     * @return CategoryInterface
     */
    function findBy(array $criteria);

    /**
     * Returns the category's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Save a Category
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    function save(CategoryInterface $category);
}