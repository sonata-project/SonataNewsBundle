<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Mailer;

use Sonata\NewsBundle\Model\CommentInterface;

interface MailerInterface
{
    /**
     * @param CommentInterface $comment
     *
     * @return mixed
     */
    public function sendCommentNotification(CommentInterface $comment);
}
