<?php

/*
 * This file is part of sonata-project.
 *
 * (c) 2010 Thomas Rabaix
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Util;

use Sonata\NewsBundle\Model\CommentInterface;

interface HashGeneratorInterface
{
    /**
     * @param \Sonata\NewsBundle\Model\CommentInterface $comment
     *
     * @return string
     */
    public function generate(CommentInterface $comment);
}
