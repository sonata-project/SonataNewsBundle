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

namespace Sonata\NewsBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\NewsBundle\Exception\NoDriverException;
use Sonata\NewsBundle\Model\NoDriverManager;

class NoDriverManagerTest extends TestCase
{
    /**
     * @dataProvider managerMethods
     */
    public function testException(string $method, array $arguments)
    {
        $this->expectException(NoDriverException::class);
        \call_user_func_array([new NoDriverManager(), $method], $arguments);
    }

    public function managerMethods()
    {
        return [
            ['getClass', []],
            ['findAll', []],
            ['findBy', [[]]],
            ['findOneBy', [[]]],
            ['find', [1]],
            ['create', []],
            ['save', [null]],
            ['delete', [null]],
            ['getTableName', []],
            ['getConnection', []],
        ];
    }
}
