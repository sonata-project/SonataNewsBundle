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

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Application\Sonata\NewsBundle\Entity\Comment;

class CommentAdmin extends Admin
{
    protected $parentAssociationMapping = 'post';

    protected $list = array(
        'name' => array('identifier' => true),
        'getStatusCode' => array('label' => 'status_code', 'type' => 'string', 'sortable' => 'status'),
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
        'status' => array('type' => 'choice'),
    );

    protected $formGroups = array(
        'General' => array(
            'fields' => array('post', 'name', 'email', 'url', 'message', 'status')
        )
    );

    protected $filter = array(
        'name',
        'email',
        'message'
    );

    public function configureFormFields(FormMapper $form)
    {
        $form->add('status', array('choices' => Comment::getStatusList()), array('type' => 'choice'));

        if(!$this->isChild()) {
            $form->add('post', array(), array('edit' => 'list'));
        }
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