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

use Bundle\Sonata\BaseApplicationBundle\Admin\EntityAdmin as Admin;

use Bundle\Sonata\BaseApplicationBundle\Admin\FieldDescription;
use Bundle\Sonata\BaseApplicationBundle\Filter\Filter;

use Application\Sonata\NewsBundle\Entity\Comment;

class PostAdmin extends Admin
{

    protected $class = 'Application\Sonata\NewsBundle\Entity\Post';

    protected $listFields = array(
        'title' => array('identifier' => true),
        'author',
        'enabled',
        'comments_enabled',
    );

    protected $formFields = array(
        'enabled',
        'title',
        'abstract',
        'content',

        'author'   => array('edit' => 'inline'),
        'tags'     => array('options' => array('expanded' => true)),
        
        'comments_close_at',
        'comments_enabled',
        'comments_default_status'
    );

    protected $formGroups = array(
        'General' => array(
            'fields' => array('author', 'title', 'abstract', 'content'),
        ),
        'Tags' => array(
            'fields' => array('tags'),
        ),
        'Options' => array(
            'fields' => array('enabled', 'comments_close_at', 'comments_enabled', 'comments_default_status'),
            'collapsed' => true
        )
    );

    protected $filterFields = array(
        'title',
        'enabled',
        'tags' => array('filter_field_options' => array('expanded' => true, 'multiple' => true))
    );

    protected $baseRoute = 'sonata_news_post_admin';

    // don't know yet how to get this value
    protected $baseControllerName = 'NewsBundle:PostAdmin';

    public function configureFormFields()
    {
        $this->formFields['comments_default_status']->setType('choice');

        $options = $this->formFields['comments_default_status']->getOption('form_field_options', array());
        $options['choices'] = Comment::getStatusList();

        $this->formFields['comments_default_status']->setOption('form_field_options', $options);
    }

    public function configureFilterFields()
    {
        $this->filterFields['with_open_comments'] = new FieldDescription;
        $this->filterFields['with_open_comments']->setName('label');
        $this->filterFields['with_open_comments']->setTemplate('Sonata\BaseApplicationBundle:CRUD:filter_callback.twig');
        $this->filterFields['with_open_comments']->setType('callback');
        $this->filterFields['with_open_comments']->setOption('filter_options', array(
            'filter' => array($this, 'getWithOpenCommentFilter'),
            'field'  => array($this, 'getWithOpenCommentField')
        ));
    }

    public function getWithOpenCommentFilter($queryBuilder, $alias, $field, $value)
    {

        if(!$value) {
            return;
        }

        $queryBuilder->leftJoin(sprintf('%s.comments', $alias), 'c');
        $queryBuilder->andWhere('c.status = :status');
        $queryBuilder->setParameter('status', \Application\Sonata\NewsBundle\Entity\Comment::STATUS_MODERATE);
    }

    public function getWithOpenCommentField(Filter $filter)
    {

        return new \Symfony\Component\Form\CheckboxField(
            $filter->getName(),
            array()
        );
    }

    public function preInsert($post)
    {
        parent::preInsert($post);

        $this->container->get('fos_user.user_manager')->updatePassword($post->getAuthor());
    }

    public function preUpdate($post)
    {
        parent::preUpdate($post);

        $this->container->get('fos_user.user_manager')->updatePassword($post->getAuthor());
    }
}