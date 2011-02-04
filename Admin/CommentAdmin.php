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

use Application\Sonata\NewsBundle\Entity\Comment;

class CommentAdmin extends Admin
{

    protected $class = 'Application\Sonata\NewsBundle\Entity\Comment';

    protected $list = array(
        'name' => array('identifier' => true),
        'getStatusCode' => array('label' => 'status_code'),
        'post',
        'email',
        'url',
        'message',
    );

    protected $form = array(
        'name',
        'email',
        'url',
        'message',
        'post' => array('edit' => 'list'),
        'status' => array('type' => 'choice'),
    );

    // don't know yet how to get this value
    protected $baseControllerName = 'SonataNewsBundle:CommentAdmin';

    public function configureFormFieldDescriptions()
    {

        $this->formFieldDescriptions['status']->setType('choice');
        $options = $this->formFieldDescriptions['status']->getOption('form_field_options', array());
        $options['choices'] = Comment::getStatusList();
//        $options['expanded'] = true;

        $this->formFieldDescriptions['status']->setOption('form_field_options', $options);
    }

    public function getBatchActions()
    {

        return array(
            'delete'    => 'action_delete',
            'enabled'   => 'enable_comments',
            'disabled'  => 'disabled_comments',
        );
    }
}