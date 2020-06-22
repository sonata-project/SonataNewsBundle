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

namespace Sonata\NewsBundle\Event;

use Sonata\NewsBundle\Model\CommentInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class CommentEvent extends Event
{
    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var CommentInterface
     */
    private $comment;

    public function __construct(CommentInterface $comment, ?Request $request = null)
    {
        $this->comment = $comment;
        $this->request = $request;
    }

    final public function getComment(): CommentInterface
    {
        return $this->comment;
    }

    final public function getRequest(): ?Request
    {
        return $this->request;
    }
}
