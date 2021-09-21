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

/**
 * NEXT_MAJOR: Remove PageableInterface extension.
 */
interface CommentManagerInterface extends ManagerInterface, PageableInterface
{
    /**
     * Update the number of comment for a comment.
     */
    public function updateCommentsCount(?PostInterface $post = null);
}
