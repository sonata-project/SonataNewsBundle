<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Form\Type;

use Sonata\CoreBundle\Form\Type\BaseStatusType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentStatusType extends BaseStatusType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choice_translation_domain' => 'SonataNewsBundle',
        ]);
    }
}
