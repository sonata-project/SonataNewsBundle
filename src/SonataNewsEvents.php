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

namespace Sonata\NewsBundle;

final class SonataNewsEvents
{
    /**
     * The COMMENT_INITIALIZE event occurs when the comment process is initialized.
     *
     * This event allows you to modify the default values of the comment before binding the form.
     *
     * @Event("Sonata\NewsBundle\Event\CommentEvent")
     */
    public const COMMENT_INITIALIZE = 'sonata_news.comment.initialize';

    /**
     * The COMMENT_SUCCESS event occurs when the comment form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Sonata\NewsBundle\Event\FormEvent")
     */
    public const COMMENT_SUCCESS = 'sonata_news.comment.success';

    /**
     * The COMMENT_COMPLETED event occurs after saving the comment in the comment process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Sonata\NewsBundle\Event\FilterCommentResponseEvent")
     */
    public const COMMENT_COMPLETED = 'sonata_news.comment.completed';
}
