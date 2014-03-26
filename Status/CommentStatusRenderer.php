<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\NewsBundle\Status;

use Sonata\CoreBundle\Component\Status\StatusClassRendererInterface;
use Sonata\NewsBundle\Model\CommentInterface;


/**
 * Class CommentStatusRenderer
 *
 * @package Sonata\NewsBundle\Status
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentStatusRenderer implements StatusClassRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function handlesObject($object, $statusName = null)
    {
        return $object instanceof CommentInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusClass($object, $statusName = null, $default = "")
    {
        switch ($object->getStatus()) {
            case CommentInterface::STATUS_INVALID:
                return "danger";
                break;
            case CommentInterface::STATUS_MODERATE:
                return "warning";
                break;
            case CommentInterface::STATUS_VALID:
                return "success";
                break;
            default:
                break;
        }
    }
}