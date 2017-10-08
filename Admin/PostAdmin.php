<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Permalink\PermalinkInterface;
use Sonata\UserBundle\Model\UserManagerInterface;

class PostAdmin extends AbstractAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var FormatterPool
     */
    protected $formatterPool;

    /**
     * @var PermalinkInterface
     */
    protected $permalinkGenerator;

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param FormatterPool $formatterPool
     */
    public function setPoolFormatter(FormatterPool $formatterPool)
    {
        $this->formatterPool = $formatterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($post)
    {
        $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($post)
    {
        $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
    }

    /**
     * @param PermalinkInterface $permalinkGenerator
     */
    public function setPermalinkGenerator(PermalinkInterface $permalinkGenerator)
    {
        $this->permalinkGenerator = $permalinkGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('author')
            ->add('enabled')
            ->add('title')
            ->add('abstract')
            ->add('content', null, ['safe' => true])
            ->add('tags')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isHorizontal = $this->getConfigurationPool()->getOption('form_type') == 'horizontal';
        $formMapper
            ->with('group_post', [
                    'class' => 'col-md-8',
                ])
                ->add('author', 'sonata_type_model_list')
                ->add('title')
                ->add('abstract', null, ['attr' => ['rows' => 5]])
                ->add('content', 'sonata_formatter_type', [
                    'event_dispatcher' => $formMapper->getFormBuilder()->getEventDispatcher(),
                    'format_field' => 'contentFormatter',
                    'source_field' => 'rawContent',
                    'source_field_options' => [
                        'horizontal_input_wrapper_class' => $isHorizontal ? 'col-lg-12' : '',
                        'attr' => ['class' => $isHorizontal ? 'span10 col-sm-10 col-md-10' : '', 'rows' => 20],
                    ],
                    'ckeditor_context' => 'news',
                    'target_field' => 'content',
                    'listener' => true,
                ])
            ->end()
            ->with('group_status', [
                    'class' => 'col-md-4',
                ])
                ->add('enabled', null, ['required' => false])
                ->add('image', 'sonata_type_model_list', ['required' => false], [
                    'link_parameters' => [
                        'context' => 'news',
                        'hide_context' => true,
                    ],
                ])

                ->add('publicationDateStart', 'sonata_type_datetime_picker', ['dp_side_by_side' => true])
                ->add('commentsCloseAt', 'sonata_type_datetime_picker', [
                    'dp_side_by_side' => true,
                    'required' => false,
                ])
                ->add('commentsEnabled', null, ['required' => false])
                ->add('commentsDefaultStatus', 'sonata_news_comment_status', ['expanded' => true])
            ->end()

            ->with('group_classification', [
                'class' => 'col-md-4',
                ])
                ->add('tags', 'sonata_type_model_autocomplete', [
                    'property' => 'name',
                    'multiple' => 'true',
                    'required' => false,
                ])
                ->add('collection', 'sonata_type_model_list', ['required' => false])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', [
                'template' => 'SonataNewsBundle:Admin:list_post_custom.html.twig',
                'label' => 'Post',
                'sortable' => 'title',
            ])
            ->add('commentsEnabled', null, ['editable' => true])
            ->add('publicationDateStart')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $that = $this;

        $datagridMapper
            ->add('title')
            ->add('enabled')
            ->add('tags', null, ['field_options' => ['expanded' => true, 'multiple' => true]])
            ->add('author')
            ->add('with_open_comments', 'doctrine_orm_callback', [
//                'callback'   => array($this, 'getWithOpenCommentFilter'),
                'callback' => function ($queryBuilder, $alias, $field, $data) use ($that) {
                    if (!is_array($data) || !$data['value']) {
                        return;
                    }

                    $queryBuilder->leftJoin(sprintf('%s.comments', $alias), 'c');
                    $queryBuilder->andWhere('c.status = :status');
                    $queryBuilder->setParameter('status', CommentInterface::STATUS_MODERATE);
                },
                'field_type' => 'checkbox',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_post'),
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );

        $menu->addChild(
            $this->trans('sidemenu.link_view_comments'),
            ['uri' => $admin->generateUrl('sonata.news.admin.comment.list', ['id' => $id])]
        );

        if ($this->hasSubject() && $this->getSubject()->getId() !== null) {
            $menu->addChild(
                'sidemenu.link_view_post',
                ['uri' => $admin->getRouteGenerator()->generate(
                    'sonata_news_view',
                    ['permalink' => $this->permalinkGenerator->generate($this->getSubject())]
                )]
            );
        }
    }
}
