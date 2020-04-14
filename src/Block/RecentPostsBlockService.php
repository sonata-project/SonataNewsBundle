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

namespace Sonata\NewsBundle\Block;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class RecentPostsBlockService extends AbstractAdminBlockService
{
    /**
     * @var PostManagerInterface
     */
    protected $manager;

    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * @param string $name
     * @param Pool   $adminPool
     */
    public function __construct($name, EngineInterface $templating, ManagerInterface $postManager, ?Pool $adminPool = null)
    {
        if (!$postManager instanceof PostManagerInterface) {
            @trigger_error(
                'Calling the '.__METHOD__.' method with a Sonata\Doctrine\Model\ManagerInterface is deprecated'
                .' since version 2.4 and will be removed in 3.0.'
                .' Use the new signature with a Sonata\NewsBundle\Model\PostManagerInterface instead.',
                E_USER_DEPRECATED
            );
        }

        $this->manager = $postManager;
        $this->adminPool = $adminPool;

        parent::__construct($name, $templating);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null)
    {
        $criteria = [
            'mode' => $blockContext->getSetting('mode'),
        ];

        $parameters = [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'pager' => $this->manager->getPager($criteria, 1, $blockContext->getSetting('number')),
            'admin_pool' => $this->adminPool,
        ];

        if ('admin' === $blockContext->getSetting('mode')) {
            return $this->renderPrivateResponse($blockContext->getTemplate(), $parameters, $response);
        }

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['number', IntegerType::class, [
                    'required' => true,
                    'label' => 'form.label_number',
                ]],
                ['title', TextType::class, [
                    'label' => 'form.label_title',
                    'required' => false,
                ]],
                ['translation_domain', TextType::class, [
                    'label' => 'form.label_translation_domain',
                    'required' => false,
                ]],
                ['icon', TextType::class, [
                    'label' => 'form.label_icon',
                    'required' => false,
                ]],
                ['class', TextType::class, [
                    'label' => 'form.label_class',
                    'required' => false,
                ]],
                ['mode', ChoiceType::class, [
                    'choices' => [
                        'form.label_mode_public' => 'public',
                        'form.label_mode_admin' => 'admin',
                    ],
                    'label' => 'form.label_mode',
                ]],
            ],
            'translation_domain' => 'SonataNewsBundle',
        ]);
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-pencil',
            'class' => null,
            'template' => '@SonataNews/Block/recent_posts.html.twig',
        ]);
    }

    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (null !== $code ? $code : $this->getName()), false, 'SonataNewsBundle', [
            'class' => 'fa fa-pencil',
        ]);
    }
}
