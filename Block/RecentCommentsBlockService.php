<?php

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
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class RecentCommentsBlockService extends AbstractAdminBlockService
{
    /**
     * @var CommentManagerInterface
     */
    protected $manager;

    /**
     * @var Pool
     */
    protected $adminPool;

    /**
     * @param string           $name
     * @param EngineInterface  $templating
     * @param ManagerInterface $commentManager
     * @param Pool             $adminPool
     */
    public function __construct($name, EngineInterface $templating, ManagerInterface $commentManager, Pool $adminPool = null)
    {
        if (!$commentManager instanceof CommentManagerInterface) {
            @trigger_error(
                'Calling the '.__METHOD__.' method with a Sonata\CoreBundle\Model\ManagerInterface is deprecated'
                .' since version 2.4 and will be removed in 3.0.'
                .' Use the new signature with a Sonata\NewsBundle\Model\CommentManagerInterface instead.',
                E_USER_DEPRECATED
            );
        }

        $this->manager = $commentManager;
        $this->adminPool = $adminPool;

        parent::__construct($name, $templating);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $criteria = array(
            'mode' => $blockContext->getSetting('mode'),
        );

        $parameters = array(
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'pager' => $this->manager->getPager($criteria, 1, $blockContext->getSetting('number')),
            'admin_pool' => $this->adminPool,
        );

        if ($blockContext->getSetting('mode') === 'admin') {
            return $this->renderPrivateResponse($blockContext->getTemplate(), $parameters, $response);
        }

        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('number', 'integer', array(
                    'required' => true,
                    'label' => 'form.label_number',
                )),
                array('title', 'text', array(
                    'required' => false,
                    'label' => 'form.label_title',
                )),
                array('mode', 'choice', array(
                    'choices' => array(
                        'public' => 'form.label_mode_public',
                        'admin' => 'form.label_mode_admin',
                    ),
                    'label' => 'form.label_mode',
                )),
            ),
            'translation_domain' => 'SonataNewsBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'number' => 5,
            'mode' => 'public',
            'title' => 'Recent Comments',
            'template' => 'SonataNewsBundle:Block:recent_comments.html.twig',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'SonataNewsBundle', array(
            'class' => 'fa fa-comments-o',
        ));
    }
}
