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

use Doctrine\ODM\MongoDB\Query\Builder;

final class MongoDBPaginator extends BasePaginator
{
    /**
     * @var Builder
     */
    protected $queryBuilder;

    public function __construct(Builder $queryBuilder, int $pageSize = BasePaginator::PAGE_SIZE)
    {
        $this->queryBuilder = $queryBuilder;
        $this->pageSize = $pageSize;
        $this->numResults = $this->computeResultsCount();
    }

    public function paginate(int $page = 1): self
    {
        $paginateQuery = clone $this->queryBuilder;

        $this->currentPage = max(1, $page);
        $firstResult = ($this->currentPage - 1) * $this->pageSize;

        $query = $paginateQuery
            ->skip($firstResult)
            ->limit($this->pageSize)
            ->getQuery();

        $this->results = $query->execute();

        return $this;
    }

    private function computeResultsCount(): int
    {
        $countQuery = clone $this->queryBuilder;

        $result = $countQuery->count()->getQuery()->execute();

        \assert(\is_int($result));

        return $result;
    }
}
