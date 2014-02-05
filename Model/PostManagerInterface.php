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

use Sonata\CoreBundle\Model\ManagerInterface;

interface PostManagerInterface extends ManagerInterface
{
    /**
     * @param string                                 $permalink
     * @param \Sonata\NewsBundle\Model\BlogInterface $blog
     *
     * @return PostInterface
     */
    public function findOneByPermalink($permalink, BlogInterface $blog);

    /**
     * Retrieve posts, based on the criteria, a page at a time.
     * Valid criteria are:
     *    enabled - boolean
     *    date - query
     *    tag - string
     *    author - 'NULL', 'NOT NULL', id, array of ids
     *
     * @param array   $criteria
     * @param integer $page
     * @param integer $maxPerPage
     *
     * @return \Sonata\AdminBundle\Datagrid\Pager
     */
    public function getPager(array $criteria, $page, $maxPerPage = 10);

    /**
     * @param string $date  Date in format YYYY-MM-DD
     * @param string $step  Interval step: year|month|day
     * @param string $alias Table alias for the publicationDateStart column
     *
     * @return array
     */
    public function getPublicationDateQueryParts($date, $step, $alias = 'p');

    /**
     * @param string $collection
     *
     * @return array
     */
    public function getPublicationCollectionQueryParts($collection);
}
