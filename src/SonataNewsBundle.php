<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataNewsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information.
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping([
            'sonata_post_comment' => 'Sonata\NewsBundle\Form\Type\CommentType',
            'sonata_news_comment_status' => 'Sonata\NewsBundle\Form\Type\CommentStatusType',
            'sonata_news_api_form_comment' => 'Sonata\NewsBundle\Form\Type\ApiCommentType',
            'sonata_news_api_form_post' => 'Sonata\NewsBundle\Form\Type\ApiPostType',
        ]);
    }
}
