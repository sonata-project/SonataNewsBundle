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

use Symfony\Component\Security\Core\User\UserInterface;

final class UserMock implements UserInterface
{
    public function getRoles()
    {
        return [];
    }

    public function getPassword()
    {
        return '';
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return '';
    }

    public function eraseCredentials()
    {
    }
}
