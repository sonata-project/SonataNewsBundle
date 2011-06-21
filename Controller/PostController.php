<?php

namespace Sonata\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\NewsBundle\Model\CommentInterface;

class PostController extends Controller
{
    public function archiveAction()
    {
        $pager = $this->get('sonata.news.manager.post')->getPager(array(), 1);

        return $this->render('SonataNewsBundle:Post:archive.html.twig', array(
            'pager' => $pager,
        ));
    }

    public function viewAction($year, $month, $day, $slug)
    {
        $post = $this->get('sonata.news.manager.post')->findOneBySlug($year, $month, $day, $slug);

        if (!$post) {
            throw new NotFoundHttpException('Unable to find the post');
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => false
        ));
    }

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

    public function addCommentFormAction($post_id, $form = false)
    {
        if (!$form) {
            $post = $this->get('sonata.news.manager.post')->findOneBy(array(
                'id' => $post_id
            ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
            'form'      => $form->createView(),
            'post_id'   => $post_id
        ));
    }

    public function getCommentForm($post)
    {
        $comment = $this->get('sonata.news.manager.comment')->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        $formBuilder = $this->get('form.factory')
            ->createNamedBuilder('form', 'comment', $comment)
            ->add('name')
            ->add('email')
            ->add('url')
            ->add('message')
        ;

        return $formBuilder->getForm();
    }

    public function addCommentAction($id)
    {
        $post = $this->get('sonata.news.manager.post')->findOneBy(array(
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
}