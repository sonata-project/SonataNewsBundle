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

namespace Sonata\NewsBundle\Util;

use Sonata\NewsBundle\Model\CommentInterface;

class HashGenerator implements HashGeneratorInterface
{
    /**
     * @var string
     */
    protected $salt;

    /**
     * @param string $salt
     */
    public function __construct($salt)
    {
        $this->salt = $salt;
    }

    public function generate(CommentInterface $comment)
    {
        return md5(sprintf('%s/%s/%s', $comment->getPost()->getId(), $comment->getId(), $this->salt));
    }
}
