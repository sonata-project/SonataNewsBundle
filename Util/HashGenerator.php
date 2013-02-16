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

class HashGenerator implements HashGeneratorInterface
{
    protected $salt;

    /**
     * @param string $salt
     */
    public function __construct($salt)
    {
        $this->salt = $salt;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(CommentInterface $comment)
    {
        return md5(sprintf('%s/%s/%s', $comment->getPost()->getId(), $comment->getId(), $this->salt));
    }
}
