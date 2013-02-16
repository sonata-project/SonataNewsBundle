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
    public function create();

    /**
     * Deletes a post
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function delete(CategoryInterface $category);

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     *
     * @return CategoryInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds one category by the given criteria
     *
     * @param array $criteria
     *
     * @return CategoryInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the category's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Save a Category
     *
     * @param CategoryInterface $category
     *
     * @return void
     */
    public function save(CategoryInterface $category);
}
