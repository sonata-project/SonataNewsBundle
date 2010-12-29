<?php

namespace Bundle\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Bundle\BaseApplicationBundle\Tool\DoctrinePager as Pager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Form;

class PostController extends Controller
{
    public function archiveAction()
    {
        $qb = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('NewsBundle:Post')
            ->findLastPostQueryBuilder(10); // todo : make this configurable


        $pager = new Pager('Application\\NewsBundle\\Entity\\Post');

        $pager->setRouter($this->get('router'));
        $pager->setRoute('news_archive');

        $pager->setQueryBuilder($qb);
        $pager->setPage($this->get('request')->get('page', 1));
        $pager->init();

        return $this->render('NewsBundle:Post:archive.twig', array(
            'pager' => $pager,
        ));
    }

    public function viewAction($year, $month, $day, $slug)
    {

        $post = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('NewsBundle:Post')
            ->findOneBy(array(
                'slug' => $slug
            ));

        if(!$post) {
            throw new NotFoundHttpException;
        }

        return $this->render('NewsBundle:Post:view.twig', array(
            'post' => $post,
        ));
    }

    public function commentsAction($post_id)
    {

        $em = $this->get('doctrine.orm.default_entity_manager');

        $comments = $em->getRepository('NewsBundle:Comment')
            ->createQueryBuilder('c')
            ->where('c.post = :post_id')
            ->orderBy('c.created_at', 'ASC')
            ->getQuery()
            ->setParameters(array(
                'post_id' => $post_id
            ))
            ->execute();


        return $this->render('NewsBundle:Post:comments.twig', array(
            'comments'  => $comments,
        ));
    }

    public function addCommentFormAction($post_id, $form = false)
    {
        if(!$form) {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $post = $em->getReference('NewsBundle:Post', $post_id);

            $form = $this->getCommentForm($post);
        }

        return $this->render('NewsBundle:Post:comment_form.twig', array(
            'form'      => $form,
            'post_id'   => $post_id
        ));

    }
    
    public function getCommentForm($post)
    {

        $this->get('session')->start();
        
        $comment = new \Application\NewsBundle\Entity\Comment;
        $comment->setPost($post);

        $form = new Form('comment', $comment, $this->get('validator'));

        $form->add(new \Symfony\Component\Form\TextField('name'));
        $form->add(new \Symfony\Component\Form\TextField('email'));
        $form->add(new \Symfony\Component\Form\UrlField('url'));
        $form->add(new \Symfony\Component\Form\TextareaField('message'));

        return $form;
    }

    public function addCommentAction($id)
    {

        $em = $this->get('doctrine.orm.default_entity_manager');
        
        $post = $em->getRepository('NewsBundle:Post')
            ->findOneBy(array(
                'id' => $id
            ));

        if(!$post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        $form = $this->getCommentForm($post);
        $form->bind($this->get('request')->get('comment'));

        if($form->isValid()) {
            $em->persist($form->getData());
            $em->flush();

            // todo : add notice

            return $this->redirect($this->generateUrl('news_view', array(
                'year'  => $post->getYear(),
                'month' => $post->getMonth(),
                'day'   => $post->getDay(),
                'slug'  => $post->getSlug()
            )));
        }

        return $this->render('NewsBundle:Post:view.twig', array(
            'post' => $post,
            'form' => $form
        ));

    }
}
