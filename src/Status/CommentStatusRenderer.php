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

namespace Sonata\NewsBundle\Status;

use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\Twig\Status\StatusClassRendererInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentStatusRenderer implements StatusClassRendererInterface
{
    public function handlesObject($object, $statusName = null)
    {
        return $object instanceof CommentInterface;
    }

    public function getStatusClass($object, $statusName = null, $default = '')
    {
        switch ($object->getStatus()) {
            case CommentInterface::STATUS_INVALID:
                return 'danger';
            case CommentInterface::STATUS_MODERATE:
                return 'warning';
            case CommentInterface::STATUS_VALID:
                return 'success';
            default:
                return null;
        }
    }
}
