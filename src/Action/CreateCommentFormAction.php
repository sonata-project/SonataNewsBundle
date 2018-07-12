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

namespace Sonata\NewsBundle\Action;

use Sonata\NewsBundle\Form\Type\CommentType;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class CreateCommentFormAction extends Controller
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PostManagerInterface
     */
    private $postManager;

    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(
        RouterInterface $router,
        PostManagerInterface $postManager,
        CommentManagerInterface $commentManager,
        FormFactoryInterface $formFactory
    ) {
        $this->router = $router;
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $postId
     * @param bool   $form
     *
     * @return Response
     */
    public function __invoke($postId, $form = false)
    {
        if (!$form) {
            $post = $this->postManager->findOneBy([
                'id' => $postId,
            ]);

            $form = $this->getCommentForm($post);
        }

        return $this->render('@SonataNews/Post/comment_form.html.twig', [
            'form' => $form->createView(),
            'post_id' => $postId,
        ]);
    }

    /**
     * @return FormInterface
     */
    private function getCommentForm(PostInterface $post)
    {
        $comment = $this->commentManager->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->formFactory->createNamed('comment', CommentType::class, $comment, [
            'action' => $this->router->generate('sonata_news_add_comment', [
                'id' => $post->getId(),
            ]),
            'method' => 'POST',
        ]);
    }
}
