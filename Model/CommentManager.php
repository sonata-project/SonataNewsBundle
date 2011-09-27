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

use Sonata\NewsBundle\Model\CommentManagerInterface;

abstract class CommentManager implements CommentManagerInterface
{
    protected $class;

    public function getClass()
    {
        return $this->class;
    }

    public function create()
    {
        return new $this->class;
    }
}