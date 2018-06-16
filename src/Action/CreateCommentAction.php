<?php

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
use Sonata\NewsBundle\Mailer\MailerInterface;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

final class CreateCommentAction extends Controller
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var BlogInterface
     */
    private $blog;

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

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(
        RouterInterface $router,
        BlogInterface $blog,
        PostManagerInterface $postManager,
        CommentManagerInterface $commentManager,
        FormFactoryInterface $formFactory,
        MailerInterface $mailer
    ) {
        $this->router = $router;
        $this->blog = $blog;
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->formFactory = $formFactory;
        $this->mailer = $mailer;
    }

    /**
     * @param string  $id
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function __invoke(Request $request, $id)
    {
        $post = $this->postManager->findOneBy([
            'id' => $id,
        ]);

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo add notice.
            return new RedirectResponse($this->router->generate('sonata_news_view', [
                'permalink' => $this->blog->getPermalinkGenerator()->generate($post),
            ]));
        }

        $form = $this->getCommentForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();

            $this->commentManager->save($comment);
            $this->mailer->sendCommentNotification($comment);

            // todo : add notice
            return new RedirectResponse($this->router->generate('sonata_news_view', [
                'permalink' => $this->blog->getPermalinkGenerator()->generate($post),
            ]));
        }

        return $this->render('@SonataNews/Post/view.html.twig', [
            'post' => $post,
            'form' => $form,
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
