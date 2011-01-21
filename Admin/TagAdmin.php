<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Admin;

use Sonata\BaseApplicationBundle\Admin\EntityAdmin as Admin;

class TagAdmin extends Admin
{
    protected $class = 'Application\Sonata\NewsBundle\Entity\Tag';

    protected $listFields = array(
        'name' => array('identifier' => true),
        'slug',
        'enabled',
    );

    protected $formFields = array(
        'id',
        'name',
        'enabled'
    );

    // don't know yet how to get this value
    protected $baseControllerName = 'SonataNewsBundle:TagAdmin';
}