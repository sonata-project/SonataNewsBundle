<?php

namespace Sonata\NewsBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataNewsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information.
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping(array(
            'sonata_post_comment'          => 'Sonata\NewsBundle\Form\Type\CommentType',
            'sonata_news_comment_status'   => 'Sonata\NewsBundle\Form\Type\CommentStatusType',
            'sonata_news_api_form_comment' => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
            'sonata_news_api_form_post'    => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
        ));
    }
}
