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

class PostAdmin extends Admin
{

    protected $class = 'Application\Sonata\NewsBundle\Entity\Post';

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
        'tags' => array('options' => array('expanded' => true)),
//        'comments_close_at',
        'comments_enabled',
        'comments_default_status'
    );

    protected $form_groups = array(
        'General' => array(
            'fields' => array('title', 'abstract', 'content'),
        ),
        'Tags' => array(
            'fields' => array('tags'),
        ),
        'Options' => array(
            'fields' => array('comments_enabled', 'comments_default_status'),
            'collapsed' => true
        )
    );

    protected $filter_fields = array(
        'title',
        'enabled',
        'tags' => array('filter_field_options' => array('expanded' => true, 'multiple' => true))
    );

    protected $base_route = 'sonata_news_post_admin';

    // don't know yet how to get this value
    protected $base_controller_name = 'NewsBundle:PostAdmin';

    public function configureFormFields()
    {
        $this->form_fields['comments_default_status']['type'] = 'choice';
        $this->form_fields['comments_default_status']['options']['choices'] = \Application\Sonata\NewsBundle\Entity\Comment::getStatusList();
    }

    public function configureFilterFields()
    {
        $this->filter_fields['with_open_comments'] = array(
            'type'           => 'callback',
            'filter_options' => array(
                'filter'  => array($this, 'getWithOpenCommentFilter'),
                'field'   => array($this, 'getWithOpenCommentField')
            )
        );
    }

    public function getWithOpenCommentFilter($query_builder, $alias, $field, $value)
    {

        if(!$value) {
            return;
        }

        $query_builder->leftJoin(sprintf('%s.comments', $alias), 'c');
        $query_builder->andWhere('c.status = :status');
        $query_builder->setParameter('status', \Application\Sonata\NewsBundle\Entity\Comment::STATUS_MODERATE);
    }

    public function getWithOpenCommentField($filter)
    {

        return new \Symfony\Component\Form\CheckboxField(
            $filter->getName(),
            array()
        );
    }
}