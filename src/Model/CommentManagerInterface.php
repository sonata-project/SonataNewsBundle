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
use Sonata\NewsBundle\Pagination\BasePaginator;

interface CommentManagerInterface extends ManagerInterface
{
    /**
     * Update the number of comment for a comment.
     */
    public function updateCommentsCount(?PostInterface $post = null);

    public function getPaginator(array $criteria = [], $page = 1, $limit = 10, array $sort = []): BasePaginator;
}
