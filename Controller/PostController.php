<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Controller;

use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\CommentInterface;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * @param array   $criteria
     * @param array   $parameters
     * @param Request $request
     *
     * @return Response
     */
    public function renderArchive(array $criteria = array(), array $parameters = array(), Request $request = null)
    {
        $request = $this->resolveRequest($request);

        $pager = $this->getPostManager()->getPager(
            $criteria,
            $request->get('page', 1)
        );

        $parameters = array_merge(array(
            'pager'            => $pager,
            'blog'             => $this->getBlog(),
            'tag'              => false,
            'collection'       => false,
            'route'            => $request->get('_route'),
            'route_parameters' => $request->get('_route_params'),
        ), $parameters);

        $response = $this->render(sprintf('SonataNewsBundle:Post:archive.%s.twig', $request->getRequestFormat()), $parameters);

        if ('rss' === $request->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function archiveAction(Request $request = null)
    {
        return $this->renderArchive();
    }

    /**
     * @param string  $tag
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function tagAction($tag, Request $request = null)
    {
        $request = $this->resolveRequest($request);

        $tag = $this->get('sonata.classification.manager.tag')->findOneBy(array(
            'slug'    => $tag,
            'enabled' => true,
        ));

        if (!$tag || !$tag->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the tag');
        }

        return $this->renderArchive(array('tag' => $tag->getSlug()), array('tag' => $tag), $request);
    }

    /**
     * @param $collection
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function collectionAction($collection, Request $request = null)
    {
        $request = $this->resolveRequest($request);

        $collection = $this->get('sonata.classification.manager.collection')->findOneBy(array(
            'slug'    => $collection,
            'enabled' => true,
        ));

        if (!$collection || !$collection->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the collection');
        }

        return $this->renderArchive(array('collection' => $collection), array('collection' => $collection), $request);
    }

    /**
     * @param string  $year
     * @param string  $month
     * @param Request $request
     *
     * @return Response
     */
    public function archiveMonthlyAction($year, $month, Request $request = null)
    {
        $request = $this->resolveRequest($request);

        return $this->renderArchive(array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, $month, 1), 'month'),
        ), $request);
    }

    /**
     * @param string  $year
     * @param Request $request
     *
     * @return Response
     */
    public function archiveYearlyAction($year, Request $request = null)
    {
        $request = $this->resolveRequest($request);

        return $this->renderArchive(array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year'),
        ), $request);
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param $permalink
     *
     * @return Response
     */
    public function viewAction($permalink)
    {
        $post = $this->getPostManager()->findOneByPermalink($permalink, $this->getBlog());

        if (!$post || !$post->isPublic()) {
            throw new NotFoundHttpException('Unable to find the post');
        }

        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                ->setTitle($post->getTitle())
                ->addMeta('name', 'description', $post->getAbstract())
                ->addMeta('property', 'og:title', $post->getTitle())
                ->addMeta('property', 'og:type', 'blog')
                ->addMeta('property', 'og:url', $this->generateUrl('sonata_news_view', array(
                    'permalink'  => $this->getBlog()->getPermalinkGenerator()->generate($post, true),
                ), UrlGeneratorInterface::ABSOLUTE_URL))
                ->addMeta('property', 'og:description', $post->getAbstract())
            ;
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => false,
            'blog' => $this->getBlog(),
        ));
    }

    /**
     * @return SeoPageInterface
     */
    public function getSeoPage()
    {
        if ($this->has('sonata.seo.page')) {
            return $this->get('sonata.seo.page');
        }

        return;
    }

    /**
     * @param int $postId
     *
     * @return Response
     */
    public function commentsAction($postId)
    {
        $pager = $this->getCommentManager()
            ->getPager(array(
                'postId'  => $postId,
                'status'  => CommentInterface::STATUS_VALID,
            ), 1, 500); //no limit

        return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
            'pager'  => $pager,
        ));
    }

    /**
     * @param $postId
     * @param bool $form
     *
     * @return Response
     */
    public function addCommentFormAction($postId, $form = false)
    {
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $postId,
            ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
            'form'      => $form->createView(),
            'post_id'   => $postId,
        ));
    }

    /**
     * @param $post
     *
     * @return FormInterface
     */
    public function getCommentForm(PostInterface $post)
    {
        $comment = $this->getCommentManager()->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->get('form.factory')->createNamed('comment', 'sonata_post_comment', $comment);
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param string  $id
     * @param Request $request
     *
     * @return Response
     */
    public function addCommentAction($id, Request $request = null)
    {
        $request = $this->resolveRequest($request);

        $post = $this->getPostManager()->findOneBy(array(
            'id' => $id,
        ));

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'permalink'  => $this->getBlog()->getPermalinkGenerator()->generate($post),
            )));
        }

        $form = $this->getCommentForm($post);
        $form->submit($request);

        if ($form->isValid()) {
            $comment = $form->getData();

            $this->getCommentManager()->save($comment);
            $this->get('sonata.news.mailer')->sendCommentNotification($comment);

            // todo : add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'permalink'  => $this->getBlog()->getPermalinkGenerator()->generate($post),
            )));
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => $form,
        ));
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
     * @param string $commentId
     * @param string $hash
     * @param string $status
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function commentModerationAction($commentId, $hash, $status)
    {
        $comment = $this->getCommentManager()->findOneBy(array('id' => $commentId));

        if (!$comment) {
            throw new AccessDeniedException();
        }

        $computedHash = $this->get('sonata.news.hash.generator')->generate($comment);

        if ($computedHash != $hash) {
            throw new AccessDeniedException();
        }

        $comment->setStatus($status);

        $this->getCommentManager()->save($comment);

        return new RedirectResponse($this->generateUrl('sonata_news_view', array(
            'permalink'  => $this->getBlog()->getPermalinkGenerator()->generate($comment->getPost()),
        )));
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
    private function resolveRequest(Request $request = null)
    {
        if (null === $request) {
            return $this->getRequest();
        }

        return $request;
    }
}
