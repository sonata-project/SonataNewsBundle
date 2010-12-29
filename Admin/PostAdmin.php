<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\NewsBundle\Admin;

use Bundle\BaseApplicationBundle\Admin\Admin;

class PostAdmin extends Admin
{

    protected $class = 'Application\NewsBundle\Entity\Post';

    protected $list_fields = array(
        'title' => array('identifier' => true),
        'slug',
        'enabled',
        'comments_enabled',
    );

    protected $form_fields = array(
        'enabled',
        'title',
        'abstract',
        'content',
        'tags',
        'comments_close_at',
        'comments_enabled',
        'comments_default_status'
    );

    protected $base_route = 'news_post_admin';

    // don't know yet how to get this value
    protected $base_controller_name = 'NewsBundle:PostAdmin';

    public function configureFormFields()
    {
        $this->form_fields['comments_default_status']['type'] = 'choice';
        $this->form_fields['comments_default_status']['options']['choices'] = \Application\NewsBundle\Entity\Comment::getStatusList();
    }
}