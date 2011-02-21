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
use Sonata\BaseApplicationBundle\Form\FormMapper;
use Sonata\BaseApplicationBundle\Datagrid\DatagridMapper;
use Sonata\BaseApplicationBundle\Datagrid\ListMapper;

use Application\Sonata\NewsBundle\Entity\Comment;

class CommentAdmin extends Admin
{
    protected $list = array(
        'name' => array('identifier' => true),
        'getStatusCode' => array('label' => 'status_code', 'type' => 'string'),
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

    protected $filter = array(
        'name',
        'email',
        'message'
    );

    public function configureFormFields(FormMapper $form)
    {
        $form->add('status', array('choices' => Comment::getStatusList()), array('type' => 'choice'));
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