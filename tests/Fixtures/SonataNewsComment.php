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

namespace Sonata\NewsBundle\Tests\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Sonata\NewsBundle\Entity\BaseComment;

/**
 * @ORM\Entity
 * @ORM\Table(name="news__comment")
 */
class SonataNewsComment extends BaseComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId()
    {
    }
}
