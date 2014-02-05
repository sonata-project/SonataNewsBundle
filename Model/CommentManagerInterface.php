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

interface CommentManagerInterface extends ManagerInterface
{
    /**
     * Update the number of comment for a comment
     *
     * @return void
     */
    public function updateCommentsCount(PostInterface $post = null);
}
