<?php

namespace Bundle\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostController extends Controller
{
    public function indexAction()
    {
        return $this->render('PostBundle:Post:index.twig');
    }
}
