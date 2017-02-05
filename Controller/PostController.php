<?php

/*
 * This file is part of the Sonata Project package.
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
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function homeAction(Request $request)
    {
        return $this->redirect($this->generateUrl('sonata_news_archive'));
    }

    /**
     * @param Request $request
     * @param array   $criteria
     * @param array   $parameters
     *
     * @return Response
     */
    public function renderArchive(Request $request, array $criteria = array(), array $parameters = array())
    {
        $pager = $this->getPostManager()->getPager(
            $criteria,
            $request->get('page', 1)
        );

        $parameters = array_merge(array(
            'pager' => $pager,
            'blog' => $this->getBlog(),
            'tag' => false,
            'collection' => false,
            'route' => $request->get('_route'),
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
    public function archiveAction(Request $request)
    {
        return $this->renderArchive($request);
    }

    /**
     * @param Request $request
     * @param string  $tag
     *
     * @return Response
     */
    public function tagAction(Request $request, $tag)
    {
        $tag = $this->get('sonata.classification.manager.tag')->findOneBy(array(
            'slug' => $tag,
            'enabled' => true,
        ));

        if (!$tag || !$tag->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the tag');
        }

        return $this->renderArchive($request, array(
            'tag' => $tag->getSlug()
        ), array(
            'tag' => $tag
        ));
    }

    /**
     * @param Request $request
     * @param string $collection
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function collectionAction(Request $request, $collection)
    {
        $collection = $this->get('sonata.classification.manager.collection')->findOneBy(array(
            'slug' => $collection,
            'enabled' => true,
        ));

        if (!$collection || !$collection->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the collection');
        }

        return $this->renderArchive($request, array(
            'collection' => $collection
        ), array(
            'collection' => $collection
        ));
    }

    /**
     * @param string  $year
     * @param string  $month
     * @param Request $request
     *
     * @return Response
     */
    public function archiveMonthlyAction(Request $request, $year, $month)
    {
        return $this->renderArchive($request, array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, $month, 1), 'month'),
        ));
    }

    /**
     * @param string  $year
     * @param Request $request
     *
     * @return Response
     */
    public function archiveYearlyAction(Request $request, $year)
    {
        return $this->renderArchive($request, array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year'),
        ));
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param $permalink
     *
     * @return Response
     */
    public function viewAction(Request $request, $permalink)
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
                    'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post, true),
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
     * @param Request $request
     * @param int     $postId
     *
     * @return Response
     */
    public function commentsAction(Request $request, $postId)
    {
        $pager = $this->getCommentManager()
            ->getPager(array(
                'postId' => $postId,
                'status' => CommentInterface::STATUS_VALID,
            ), 1, 500); //no limit

        return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * @param Request $request
     * @param string        $postId
     * @param bool    $form
     *
     * @return Response
     */
    public function addCommentFormAction(Request $request, $postId, $form = false)
    {
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $postId,
            ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
            'form' => $form->createView(),
            'post_id' => $postId,
        ));
    }

    /**
     * @param PostInterface $post
     *
     * @return FormInterface
     */
    public function getCommentForm(PostInterface $post)
    {
        $comment = $this->getCommentManager()->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->get('form.factory')->createNamed('comment', 'sonata_post_comment', $comment, array(
            'action' => $this->generateUrl('sonata_news_add_comment', array(
                'id' => $post->getId(),
            )),
            'method' => 'POST',
        ));
    }

    /**
     * @throws NotFoundHttpException
     *
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     */
    public function addCommentAction(Request $request, $id)
    {
        $post = $this->getPostManager()->findOneBy(array(
            'id' => $id,
        ));

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post),
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
                'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($post),
            )));
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => $form,
        ));
    }

    /**
     * @param Request $request
     * @param string  $commentId
     * @param string  $hash
     * @param string  $status
     *
     * @return RedirectResponse
     */
    public function commentModerationAction(Request $request, $commentId, $hash, $status)
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
            'permalink' => $this->getBlog()->getPermalinkGenerator()->generate($comment->getPost()),
        )));
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
}
