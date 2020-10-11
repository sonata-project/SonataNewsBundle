<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use Sonata\DatagridBundle\Pager\PageableInterface;
use Sonata\Doctrine\Model\ManagerInterface;

interface PostManagerInterface extends ManagerInterface, PageableInterface
{
    /**
     * @param string $permalink
     *
     * @return PostInterface
     */
    public function findOneByPermalink($permalink, BlogInterface $blog);

    /**
     * @param string $date  Date in format YYYY-MM-DD
     * @param string $step  Interval step: year|month|day
     * @param string $alias Table alias for the publicationDateStart column
     *
     * @return array
     */
    public function getPublicationDateQueryParts($date, $step, $alias = 'p');
}
