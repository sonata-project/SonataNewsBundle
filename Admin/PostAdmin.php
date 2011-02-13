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

class PostAdmin extends Admin
{

    protected $class = 'Application\Sonata\NewsBundle\Entity\Post';
    protected $baseControllerName = 'SonataNewsBundle:PostAdmin';

    protected $formOptions = array(
        'validation_groups' => 'admin'
    );

    protected $list = array(
        'title' => array('identifier' => true),
        'author',
        'enabled',
        'commentsEnabled',
    );

    protected $form = array(
        'author' => array('edit' => 'list'),
        'enabled',
        'title',
        'abstract',
        'content',
        'tags'     => array('form_field_options' => array('expanded' => true)),
        'commentsCloseAt',
        'commentsEnabled',
    );

    protected $formGroups = array(
        'General' => array(
            'fields' => array('author', 'title', 'abstract', 'content'),
        ),
        'Tags' => array(
            'fields' => array('tags'),
        ),
        'Options' => array(
            'fields' => array('enabled', 'commentsCloseAt', 'commentsEnabled', 'commentsDefaultStatus'),
            'collapsed' => true
        )
    );

    protected $filter = array(
        'title',
        'enabled',
        'tags' => array('filter_field_options' => array('expanded' => true, 'multiple' => true))
    );

    public function configureFormFields(FormMapper $form)
    {
        $form->add('commentsDefaultStatus', array('choices' => Comment::getStatusList()), array('type' => 'choice'));
    }

    public function configureDatagridFilters(DatagridMapper $datagrid)
    {

        $datagrid->add('with_open_comments', array(
            'template' => 'SonataBaseApplicationBundle:CRUD:filter_callback.html.twig',
            'type' => 'callback',
            'filter_options' => array(
                'filter' => array($this, 'getWithOpenCommentFilter'),
                'field'  => array($this, 'getWithOpenCommentField')
            )
        ));
    }

    public function getWithOpenCommentFilter($queryBuilder, $alias, $field, $value)
    {

        if (!$value) {
            return;
        }

        $queryBuilder->leftJoin(sprintf('%s.comments', $alias), 'c');
        $queryBuilder->andWhere('c.status = :status');
        $queryBuilder->setParameter('status', \Application\Sonata\NewsBundle\Entity\Comment::STATUS_MODERATE);
    }

    public function getWithOpenCommentField($filter)
    {

        return new \Symfony\Component\Form\CheckboxField(
            $filter->getName(),
            array()
        );
    }

    public function preInsert($post)
    {
        parent::preInsert($post);

        if (isset($this->formFieldDescriptions['author'])) {
            $this->container->get('fos_user.user_manager')->updatePassword($post->getAuthor());
        }
    }

    public function preUpdate($post)
    {
        parent::preUpdate($post);

        if (isset($this->formFieldDescriptions['author'])) {
            $this->container->get('fos_user.user_manager')->updatePassword($post->getAuthor());
        }
    }
}