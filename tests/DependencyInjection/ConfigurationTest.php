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

namespace Sonata\NewsBundle\Tests;

use PHPUnit\Framework\TestCase;
use Sonata\NewsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testOptions()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [[
            'title' => 'Foo title',
            'link' => '/foo/bar',
            'description' => 'Foo description',
            'salt' => 'pepper',
        ]]);

        $this->assertSame('news__post_tag', $config['table']['post_tag']);
    }
}
