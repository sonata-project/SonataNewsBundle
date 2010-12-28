<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bundle\NewsBundle\Controller;

use Bundle\BaseApplicationBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Form\Form;

class TagAdminController extends Controller
{

    protected $class = 'Application\NewsBundle\Entity\Tag';

    protected $list_fields = array(
        'id',
        'name' ,
        'slug',
        'enabled',
    );

    protected $form_fields = array(
        'name',
        'enabled'
    );

    protected $base_route = 'news_tag_admin';

    // don't know yet how to get this value
    protected $base_controller_name = 'NewsBundle:TagAdmin';
}