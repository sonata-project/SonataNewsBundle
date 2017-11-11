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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\NewsBundle\Form\Type\CommentStatusType;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentAdmin extends AbstractAdmin
{
    protected $parentAssociationMapping = 'post';

    /**
     * @var CommentManagerInterface
     */
    protected $commentManager;

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['enabled'] = [
            'label' => $this->getLabelTranslatorStrategy()->getLabel('enable', 'batch', 'comment'),
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation' => false,
        ];

        $actions['disabled'] = [
            'label' => $this->getLabelTranslatorStrategy()->getLabel('disable', 'batch', 'comment'),
            'translation_domain' => $this->getTranslationDomain(),
            'ask_confirmation' => false,
        ];

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        $this->updateCountsComment();
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove($object)
    {
        $this->updateCountsComment();
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        $this->updateCountsComment();
    }

    /**
     * @param ManagerInterface $commentManager
     */
    public function setCommentManager(ManagerInterface $commentManager)
    {
        if (!$commentManager instanceof CommentManagerInterface) {
            @trigger_error(
                'Calling the '.__METHOD__.' method with a Sonata\CoreBundle\Model\ManagerInterface is deprecated'
                .' since version 2.4 and will be removed in 3.0.'
                .' Use the new signature with a Sonata\NewsBundle\Model\CommentManagerInterface instead.',
                E_USER_DEPRECATED
            );
        }

        $this->commentManager = $commentManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->with('group_comment', ['class' => 'col-md-6'])->end()
            ->with('group_general', ['class' => 'col-md-6'])->end()
        ;

        if (!$this->isChild()) {
            $formMapper
                ->with('group_general')
                    ->add('post', ModelListType::class)
                ->end()
            ;
        }

        $formMapper
            ->with('group_general')
                ->add('name')
                ->add('email')
                ->add('url', null, ['required' => false])
            ->end()
            ->with('group_comment')
                ->add('status', CommentStatusType::class, [
                    'expanded' => true,
                    'multiple' => false,
                ])
                ->add('message', null, ['attr' => ['rows' => 6]])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('getStatusCode', TextType::class, ['label' => 'status_code', 'sortable' => 'status'])
        ;

        if (!$this->isChild()) {
            $listMapper->add('post');
        }

        $listMapper
            ->add('email')
            ->add('url')
            ->add('message');
    }

    /**
     * Update the count comment.
     */
    private function updateCountsComment()
    {
        $this->commentManager->updateCommentsCount();
    }
}
