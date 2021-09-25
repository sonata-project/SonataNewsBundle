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

namespace Sonata\NewsBundle\Pagination;

abstract class BasePaginator
{
    public const PAGE_SIZE = 10;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var \Traversable
     */
    protected $results;

    /**
     * @var int
     */
    protected $numResults;

    /**
     * @return self
     */
    abstract public function paginate(int $page = 1);

    final public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    final public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    final public function getPageSize(): int
    {
        return $this->pageSize;
    }

    final public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    final public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    final public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    final public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    final public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    final public function getNumResults(): int
    {
        return $this->numResults;
    }

    final public function getResults(): \Traversable
    {
        return $this->results;
    }
}
