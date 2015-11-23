<?php

namespace Sonata\NewsBundle\Tests;

use Sonata\NewsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testOptions()
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), array(array(
            'title'       => 'Foo title',
            'link'        => '/foo/bar',
            'description' => 'Foo description',
            'salt'        => 'pepper',
        )));

        $this->assertSame('news__post_tag', $config['table']['post_tag']);
    }
}
