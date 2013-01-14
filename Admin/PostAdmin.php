<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Model\CommentManagerInterface;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PostAdmin extends Admin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var Pool
     */
    protected $formatterPool;

    protected $commentManager;

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
            ->add('content', null, array('safe' => true))
            ->add('tags')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $commentClass = $this->commentManager->getClass();

        $formMapper
            ->with('General')
                ->add('enabled', null, array('required' => false))
                ->add('author', 'sonata_type_model')
                ->add('category', 'sonata_type_model_list', array('required' => false))
                ->add('title')
                ->add('abstract')
                ->add('contentFormatter', 'sonata_formatter_type_selector', array(
                    'source' => 'rawContent',
                    'target' => 'content'
                ))
                ->add('rawContent')
            ->end()
            ->with('Tags')
                ->add('tags', 'sonata_type_model', array('expanded' => true, 'multiple' => true))
            ->end()
            ->with('Options')
                ->add('publicationDateStart')
                ->add('commentsCloseAt')
                ->add('commentsEnabled', null, array('required' => false))
                ->add('commentsDefaultStatus', 'choice', array('choices' => $commentClass::getStatusList(), 'expanded' => true))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('author')
            ->add('category')
            ->add('enabled', null, array('editable' => true))
            ->add('tags')
            ->add('commentsEnabled', null, array('editable' => true))
            ->add('commentsCount')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('enabled')
            ->add('tags', null, array('field_options' => array('expanded' => true, 'multiple' => true)))
            ->add('author')
            ->add('with_open_comments', 'doctrine_orm_callback', array(
//                'callback'   => array($this, 'getWithOpenCommentFilter'),
                'callback' => function ($queryBuilder, $alias, $field, $data) {
                    if (!is_array($data) || !$data['value']) {
                        return;
                    }

                    $commentClass = $this->commentManager->getClass();

                    $queryBuilder->leftJoin(sprintf('%s.comments', $alias), 'c');
                    $queryBuilder->andWhere('c.status = :status');
                    $queryBuilder->setParameter('status', $commentClass::STATUS_MODERATE);
                },
                'field_type' => 'checkbox'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     *
    public function getWithOpenCommentFilter($queryBuilder, $alias, $field, $value)
    {
        if (!is_array($data) || !$data['value']) {
            return;
        }

        $queryBuilder->leftJoin(sprintf('%s.comments', $alias), 'c');
        $queryBuilder->andWhere('c.status = :status');
        $queryBuilder->setParameter('status', Comment::STATUS_MODERATE);
    }*/

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_view_post'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_view_comments'),
            array('uri' => $admin->generateUrl('sonata.news.admin.comment.list', array('id' => $id)))
        );
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool $formatterPool
     *
     * @return void
     */
    public function setPoolFormatter(FormatterPool $formatterPool)
    {
        $this->formatterPool = $formatterPool;
    }

    /**
     * @return \Sonata\FormatterBundle\Formatter\Pool
     */
    public function getPoolFormatter()
    {
        return $this->formatterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($post)
    {
        $post->setContent($this->getPoolFormatter()->transform($post->getContentFormatter(), $post->getRawContent()));
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($post)
    {
        $post->setContent($this->getPoolFormatter()->transform($post->getContentFormatter(), $post->getRawContent()));
    }

    /**
     * @param CommentManagerInterface $commentManager
     *
     * @return void
     */
    public function setCommentManager(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }
}
