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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Application\Sonata\NewsBundle\Entity\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;

class CommentAdmin extends Admin
{
    protected $parentAssociationMapping = 'post';

    protected $commentManager;

    /**
     * @param \Sonata\NewsBundle\Model\CommentManagerInterface $commentManager
     * @return void
     */
    public function setCommentManager(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if(!$this->isChild()) {
            $formMapper->add('post', 'sonata_type_model', array(), array('edit' => 'list'));
//            $formMapper->add('post', 'sonata_type_admin', array(), array('edit' => 'inline'));
        }

        $formMapper
            ->add('name')
            ->add('email')
            ->add('url', null, array('required' => false))
            ->add('message')
            ->add('status', 'choice', array('choices' => Comment::getStatusList(), 'expanded' => true, 'multiple' => false))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('email')
            ->add('message')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('getStatusCode', 'text', array('label' => 'status_code', 'sortable' => 'status'))
            ->add('post')
            ->add('email')
            ->add('url')
            ->add('message');
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['enabled'] = array(
            'label' => $this->trans('batch_enable_comments'),
            'ask_confirmation' => false,
        );

        $actions['disabled'] = array(
            'label' => $this->trans('batch_disable_comments'),
            'ask_confirmation' => false
        );

        return $actions;
    }

    private function updateCountsComment()
    {
        $this->commentManager->updateCommentsCount();
    }

    public function postPersist($object)
    {
        $this->updateCountsComment();
    }

    public function postRemove($object)
    {
        $this->updateCountsComment();
    }

    public function postUpdate($object)
    {
        $this->updateCountsComment();
    }
}