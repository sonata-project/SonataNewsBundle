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

use Application\Sonata\NewsBundle\Entity\Comment;

class CommentAdmin extends Admin
{

    protected $class = 'Application\Sonata\NewsBundle\Entity\Comment';

    protected $listFields = array(
        'name' => array('identifier' => true),
        'getStatusCode' => array('label' => 'status_code'),
        'post',
        'email',
        'url',
        'message',
    );

    protected $formFields = array(
        'name',
        'email',
        'url',
        'message',
        'post',
        'status' => array('type' => 'choice'),
    );

    protected $baseRoute = 'sonata_news_comment_admin';

    // don't know yet how to get this value
    protected $baseControllerName = 'NewsBundle:CommentAdmin';

    public function configureFormFields()
    {

        $this->formFields['status']->setType('choice');
        $options = $this->formFields['status']->getOption('form_field_options', array());
        $options['choices'] = Comment::getStatusList();
        $options['expanded'] = true;

        $this->formFields['status']->setOption('form_field_options', $options);
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