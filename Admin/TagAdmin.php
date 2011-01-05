<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\Sonata\NewsBundle\Admin;

use Bundle\Sonata\BaseApplicationBundle\Admin\Admin;

class TagAdmin extends Admin
{
    protected $class = 'Application\Sonata\NewsBundle\Entity\Tag';

    protected $list_fields = array(
        'name' => array('identifier' => true),
        'slug',
        'enabled',
    );

    protected $form_fields = array(
        'name',
        'enabled'
    );

    protected $base_route = 'sonata_news_tag_admin';

    // don't know yet how to get this value
    protected $base_controller_name = 'NewsBundle:TagAdmin';
}