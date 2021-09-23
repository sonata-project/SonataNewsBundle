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

use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\NewsBundle\Exception\NoDriverException;

/**
 * @internal
 *
 * @author Christian Gripp <mail@core23.de>
 */
final class NoDriverManager implements PostManagerInterface, CommentManagerInterface
{
    public function getClass()
    {
        throw new NoDriverException();
    }

    public function findAll()
    {
        throw new NoDriverException();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        throw new NoDriverException();
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        throw new NoDriverException();
    }

    /**
     * @param mixed $id
     */
    public function find($id)
    {
        throw new NoDriverException();
    }

    public function create()
    {
        throw new NoDriverException();
    }

    /**
     * @param object $entity
     * @param bool   $andFlush
     */
    public function save($entity, $andFlush = true)
    {
        throw new NoDriverException();
    }

    /**
     * @param object $entity
     * @param bool   $andFlush
     */
    public function delete($entity, $andFlush = true)
    {
        throw new NoDriverException();
    }

    public function getTableName()
    {
        throw new NoDriverException();
    }

    public function getConnection()
    {
        throw new NoDriverException();
    }

    public function updateCommentsCount(?PostInterface $post = null)
    {
        throw new NoDriverException();
    }

    public function getPager(array $criteria, int $page, int $limit = 10, array $sort = []): PagerInterface
    {
        throw new NoDriverException();
    }

    public function findOneByPermalink($permalink, BlogInterface $blog)
    {
        throw new NoDriverException();
    }

    public function getPublicationDateQueryParts($date, $step, $alias = 'p')
    {
        throw new NoDriverException();
    }
}
