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

namespace Sonata\NewsBundle\Controller;

// NEXT_MAJOR: remove this file

@trigger_error(
    'The '.__NAMESPACE__.'\PostController class is deprecated since version 3.5 and will be removed in 4.0.'
    .' Use '.__NAMESPACE__.'\Action\* classes instead.',
    E_USER_DEPRECATED
);

use Sonata\NewsBundle\Action\CollectionPostArchiveAction;
use Sonata\NewsBundle\Action\CommentListAction;
use Sonata\NewsBundle\Action\CreateCommentAction;
use Sonata\NewsBundle\Action\CreateCommentFormAction;
use Sonata\NewsBundle\Action\ModerateCommentAction;
use Sonata\NewsBundle\Action\MonthlyPostArchiveAction;
use Sonata\NewsBundle\Action\PostArchiveAction;
use Sonata\NewsBundle\Action\TagPostArchiveAction;
use Sonata\NewsBundle\Action\ViewPostAction;
use Sonata\NewsBundle\Action\YearlyPostArchiveAction;
use Sonata\NewsBundle\Form\Type\CommentType;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function homeAction()
    {
        return $this->redirect($this->generateUrl('sonata_news_archive'));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderArchive(array $criteria = [], array $parameters = [], ?Request $request = null)
    {
        $action = $this->container->get(PostArchiveAction::class);

        return $action->renderArchive($this->resolveRequest($request), $criteria, $parameters);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function archiveAction(?Request $request = null)
    {
        $action = $this->container->get(PostArchiveAction::class);

        return $action($this->resolveRequest($request));
    }

    /**
     * @param string  $tag
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function tagAction($tag, ?Request $request = null)
    {
        $action = $this->container->get(TagPostArchiveAction::class);

        return $action($this->resolveRequest($request), $tag);
    }

    /**
     * @param string  $collection
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function collectionAction($collection, ?Request $request = null)
    {
        $action = $this->container->get(CollectionPostArchiveAction::class);

        return $action($this->resolveRequest($request), $collection);
    }

    /**
     * @param string  $year
     * @param string  $month
     * @param Request $request
     *
     * @return Response
     */
    public function archiveMonthlyAction($year, $month, ?Request $request = null)
    {
        $action = $this->container->get(MonthlyPostArchiveAction::class);

        return $action($this->resolveRequest($request), $year, $month);
    }

    /**
     * @param string  $year
     * @param Request $request
     *
     * @return Response
     */
    public function archiveYearlyAction($year, ?Request $request = null)
    {
        $action = $this->container->get(YearlyPostArchiveAction::class);

        return $action($this->resolveRequest($request), $year);
    }

    /**
     * @param string $permalink
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewAction($permalink)
    {
        $action = $this->container->get(ViewPostAction::class);

        return $action($permalink);
    }

    /**
     * @return SeoPageInterface|null
     */
    public function getSeoPage()
    {
        if ($this->has('sonata.seo.page')) {
            return $this->get('sonata.seo.page');
        }

        return null;
    }

    /**
     * @param int $postId
     *
     * @return Response
     */
    public function commentsAction($postId)
    {
        $action = $this->container->get(CommentListAction::class);

        return $action($postId);
    }

    /**
     * @param $postId
     * @param bool $form
     *
     * @return Response
     */
    public function addCommentFormAction($postId, $form = false)
    {
        $action = $this->container->get(CreateCommentFormAction::class);

        return $action($postId, $form);
    }

    /**
     * @return FormInterface
     */
    public function getCommentForm(PostInterface $post)
    {
        $comment = $this->getCommentManager()->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->get('form.factory')->createNamed('comment', CommentType::class, $comment, [
            'action' => $this->generateUrl('sonata_news_add_comment', [
                'id' => $post->getId(),
            ]),
            'method' => 'POST',
        ]);
    }

    /**
     * @param string  $id
     * @param Request $request
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function addCommentAction($id, ?Request $request = null)
    {
        $action = $this->container->get(CreateCommentAction::class);

        return $action($this->resolveRequest($request), $id);
    }

    /**
     * @param string $commentId
     * @param string $hash
     * @param string $status
     *
     * @throws AccessDeniedException
     *
     * @return RedirectResponse
     */
    public function commentModerationAction($commentId, $hash, $status)
    {
        $action = $this->container->get(ModerateCommentAction::class);

        return $action($commentId, $hash, $status);
    }

    /**
     * @return PostManagerInterface
     */
    protected function getPostManager()
    {
        return $this->get('sonata.news.manager.post');
    }

    /**
     * @return CommentManagerInterface
     */
    protected function getCommentManager()
    {
        return $this->get('sonata.news.manager.comment');
    }

    /**
     * @return BlogInterface
     */
    protected function getBlog()
    {
        return $this->get('sonata.news.blog');
    }

    /**
     * To keep backwards compatibility with older Sonata News code.
     *
     * @internal
     *
     * @param Request $request
     *
     * @return Request
     */
    private function resolveRequest(?Request $request = null)
    {
        if (null === $request) {
            return $this->get('request_stack')->getCurrentRequest();
        }

        return $request;
    }
}
