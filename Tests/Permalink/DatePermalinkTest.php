<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Tests\Model;

use Sonata\NewsBundle\Permalink\DatePermalink;
use Sonata\NewsBundle\Model\PostInterface;

class DatePermalinkTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->any())->method('getSlug')->will($this->returnValue('the-slug'));
        $post->expects($this->any())->method('getYear')->will($this->returnValue('2011'));
        $post->expects($this->any())->method('getMonth')->will($this->returnValue('12'));
        $post->expects($this->any())->method('getDay')->will($this->returnValue('30'));

        $permalink = new DatePermalink();
        $this->assertEquals('2011/12/30/the-slug', $permalink->generate($post));
    }

    public function testCustomFormating()
    {
        $post = $this->getMock('Sonata\NewsBundle\Model\PostInterface');
        $post->expects($this->any())->method('getSlug')->will($this->returnValue('the-slug'));
        $post->expects($this->any())->method('getYear')->will($this->returnValue('2011'));
        $post->expects($this->any())->method('getMonth')->will($this->returnValue('2'));
        $post->expects($this->any())->method('getDay')->will($this->returnValue('01'));

        $permalink = new DatePermalink('%1$02d/%2$02d/%3$02d/%4$s');
        $this->assertEquals('2011/02/01/the-slug', $permalink->generate($post));
    }

    public function testGetParameters()
    {
        $permalink = new DatePermalink();
        $expected = array(
            'year' => '2011',
            'month' => '12',
            'day' => '30',
            'slug' => 'the-slug',
        );

        $this->assertEquals($expected, $permalink->getParameters('2011/12/30/the-slug'));
    }

    public function testGetParametersWithWrongUrl()
    {
        $this->setExpectedException('InvalidArgumentException');

        $permalink = new DatePermalink();
        $expected = array(
            'year' => '2011',
            'month' => '12',
            'day' => '30',
            'slug' => 'the-slug',
        );

        $this->assertEquals($expected, $permalink->getParameters('2011/12/the-slug'));
    }
}
