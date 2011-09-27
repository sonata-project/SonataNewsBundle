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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\NewsBundle\Model\CommentInterface;

class PostController extends Controller
{
    /**
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
     */
    public function homeAction()
    {
        return $this->redirect($this->generateUrl('sonata_news_archive'));
    }

    /**
     * @param array $criteria
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function renderArchive(array $criteria = array(), array $parameters = array())
    {
        $pager = $this->getPostManager()->getPager(
            $criteria,
            $this->getRequest()->get('page', 1)
        );

        $parameters = array_merge(array(
            'pager' => $pager,
            'blog'  => $this->get('sonata.news.blog'),
            'tag'   => false
        ), $parameters);

        $response = $this->render(sprintf('SonataNewsBundle:Post:archive.%s.twig', $this->getRequest()->getRequestFormat()), $parameters);

        if ('rss' === $this->getRequest()->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function archiveAction()
    {
        return $this->renderArchive();
    }

    /**
     * @param $tag
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function tagAction($tag)
    {
        $tag = $this->get('sonata.news.manager.tag')->findOneBy(array(
            'slug' => $tag,
            'enabled' => true
        ));

        if (!$tag) {
            throw new NotFoundHttpException('Unable to find the tag');
        }

        return $this->renderArchive(array('tag' => $tag), array('tag' => $tag));
    }

    /**
     * @param $year
     * @param $month
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function archiveMonthlyAction($year, $month)
    {
        return $this->renderArchive(array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, $month, 1), 'month')
        ));
    }

    /**
     * @param $year
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function archiveYearlyAction($year)
    {
        return $this->renderArchive(array(
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year')
        ));
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param $year
     * @param $month
     * @param $day
     * @param $slug
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function viewAction($year, $month, $day, $slug)
    {
        $post = $this->getPostManager()->findOneBySlug($year, $month, $day, $slug);

        if (!$post) {
            throw new NotFoundHttpException('Unable to find the post');
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => false,
            'blog' => $this->get('sonata.news.blog')
        ));
    }

    /**
     * @param $post_id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function commentsAction($post_id)
    {
        $pager = $this->get('sonata.news.manager.comment')
            ->getPager(array(
                'postId' => $post_id,
                'status'  => CommentInterface::STATUS_VALID
            ), 1);

        return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
            'pager'  => $pager,
        ));
    }

    /**
     * @param $post_id
     * @param bool $form
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function addCommentFormAction($post_id, $form = false)
    {
        if (!$form) {
            $post = $this->getPostManager()->findOneBy(array(
                'id' => $post_id
            ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
            'form'      => $form->createView(),
            'post_id'   => $post_id
        ));
    }

    /**
     * @param $post
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCommentForm($post)
    {
        $comment = $this->get('sonata.news.manager.comment')->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->get('form.factory')->createNamed('sonata_post_comment', 'comment', $comment);
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addCommentAction($id)
    {
        $post = $this->getPostManager()->findOneBy(array(
            'id' => $id
        ));

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'year'  => $post->getYear(),
                'month' => $post->getMonth(),
                'day'   => $post->getDay(),
                'slug'  => $post->getSlug()
            )));
        }

        $form = $this->getCommentForm($post);
        $form->bindRequest($this->get('request'));

        if ($form->isValid()) {
            $this->get('sonata.news.manager.comment')->save($form->getData());

            // todo : add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'year'  => $post->getYear(),
                'month' => $post->getMonth(),
                'day'   => $post->getDay(),
                'slug'  => $post->getSlug()
            )));
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => $form
        ));
    }

    /**
     * @return \Sonata\NewsBundle\Model\PostManagerInterface
     */
    protected function getPostManager()
    {
        return $this->get('sonata.news.manager.post');
    }
}
