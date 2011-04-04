<?php

namespace Sonata\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sonata\AdminBundle\Tool\DoctrinePager as Pager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
    
use Application\Sonata\NewsBundle\Entity\Comment;

class PostController extends Controller
{
    public function archiveAction()
    {
        $qb = $this->get('sonata.news.entity_manager')
            ->getRepository('Application\Sonata\NewsBundle\Entity\Post')
            ->findLastPostQueryBuilder(10); // todo : make this configurable

        $pager = new Pager('Application\Sonata\NewsBundle\Entity\Post');

        $pager->setQueryBuilder($qb);
        $pager->setPage($this->get('request')->get('page', 1));
        $pager->init();

        return $this->render('SonataNewsBundle:Post:archive.html.twig', array(
            'pager' => $pager,
        ));
    }

    public function viewAction($year, $month, $day, $slug)
    {

        $post = $this->get('sonata.news.entity_manager')
            ->getRepository('Application\Sonata\NewsBundle\Entity\Post')
            ->findOneBy(array(
                'slug' => $slug,
            ));

        if (!$post) {
            throw new NotFoundHttpException;
        }

        return $this->render('SonataNewsBundle:Post:view.html.twig', array(
            'post' => $post,
            'form' => false
        ));
    }

    public function commentsAction($post_id)
    {

        $em = $this->get('sonata.news.entity_manager');

        $comments = $em->getRepository('Application\Sonata\NewsBundle\Entity\Comment')
            ->createQueryBuilder('c')
            ->where('c.post = :post_id AND c.status = :status')
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->setParameters(array(
                'post_id'   => $post_id,
                'status'    => Comment::STATUS_VALID,
            ))
            ->execute();


        return $this->render('SonataNewsBundle:Post:comments.html.twig', array(
            'comments'  => $comments,
        ));
    }

    public function addCommentFormAction($post_id, $form = false)
    {
        if (!$form) {
            $em = $this->get('sonata.news.entity_manager');

            $post = $em->getRepository('Application\Sonata\NewsBundle\Entity\Post')
                ->findOneBy(array(
                    'id' => $post_id
                ));

            $form = $this->getCommentForm($post);
        }

        return $this->render('SonataNewsBundle:Post:comment_form.html.twig', array(
            'form'      => $form,
            'post_id'   => $post_id
        ));
    }
    
    public function getCommentForm($post)
    {

        $this->get('session')->start();
        
        $comment = new Comment;
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());
        
        $form = new Form('comment', array(
            'data' => $comment,
            'validator' => $this->get('validator')
        ));

        $form->add(new \Symfony\Component\Form\TextField('name'));
        $form->add(new \Symfony\Component\Form\TextField('email'));
        $form->add(new \Symfony\Component\Form\UrlField('url'));
        $form->add(new \Symfony\Component\Form\TextareaField('message'));

        return $form;
    }

    public function addCommentAction($id)
    {

        $em = $this->get('sonata.news.entity_manager');
        
        $post = $em->getRepository('Application\Sonata\NewsBundle\Entity\Post')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable())
        {

            // todo add notice
            return new RedirectResponse($this->generateUrl('sonata_news_view', array(
                'year'  => $post->getYear(),
                'month' => $post->getMonth(),
                'day'   => $post->getDay(),
                'slug'  => $post->getSlug()
            )));
        }

        $form = $this->getCommentForm($post);
        $form->bind($this->get('request'));

        if ($form->isValid()) {

            $em->persist($form->getData());
            $em->flush();

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
