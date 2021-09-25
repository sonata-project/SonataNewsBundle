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

use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\Doctrine\Model\PageableManagerInterface;

/**
 * NEXT_MAJOR: Remove PageableManagerInterface extension.
 *
 * @method PostInterface findOneByPermalink(string $permalink, BlogInterface $blog)
 * @method array         getPublicationDateQueryParts($date, $step, $alias = 'p')
 */
interface PostManagerInterface extends ManagerInterface, PageableManagerInterface
{
    // NEXT_MAJOR: uncomment methods below.
    //public function findOneByPermalink(string $permalink, BlogInterface $blog): PostInterface;

    ///**
    // * @param string $date  Date in format YYYY-MM-DD
    // * @param string $step  Interval step: year|month|day
    // * @param string $alias Table alias for the publicationDateStart column
    // */
    //public function getPublicationDateQueryParts(string $date, string $step, string $alias = 'p'): array;
}
