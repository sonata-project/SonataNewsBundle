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
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\EditableBlockService;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Meta\MetadataInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class RecentCommentsBlockService extends AbstractBlockService implements EditableBlockService
{
    /**
     * @var CommentManagerInterface
     */
    protected $manager;

    /**
     * @var Pool
     */
    protected $adminPool;

    public function __construct(Environment $twig, CommentManagerInterface $commentManager, Pool $adminPool)
    {
        $this->manager = $commentManager;
        $this->adminPool = $adminPool;

        parent::__construct($twig);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $criteria = [
            'mode' => $blockContext->getSetting('mode'),
        ];

        $parameters = [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'pager' => $this->manager->getPaginator($criteria, 1, $blockContext->getSetting('number')),
            'admin_pool' => $this->adminPool,
        ];

        if ('admin' === $blockContext->getSetting('mode')) {
            return $this->renderPrivateResponse($blockContext->getTemplate(), $parameters, $response);
        }

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
        $form->add('settings', ImmutableArrayType::class, [
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

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-comments',
            'class' => null,
            'template' => '@SonataNews/Block/recent_comments.html.twig',
        ]);
    }

    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function validate(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata($this->getName(), (null !== $code ? $code : $this->getName()), false, 'SonataNewsBundle', [
            'class' => 'fa fa-comments-o',
        ]);
    }
}
