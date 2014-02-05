<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\NewsBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sonata\AdminBundle\Datagrid\Pager;
use Sonata\NewsBundle\Mailer\MailerInterface;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\Post;

use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class PostController
 *
 * @package Sonata\NewsBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class PostController
{
    /**
     * @var PostManagerInterface
     */
    protected $postManager;

    /**
     * @var CommentManagerInterface::
     */
    protected $commentManager;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor
     *
     * @param PostManagerInterface    $postManager
     * @param CommentManagerInterface $commentManager
     * @param MailerInterface         $mailer
     * @param FormFactoryInterface    $formFactory
     */
    public function __construct(PostManagerInterface $postManager, CommentManagerInterface $commentManager, MailerInterface $mailer, FormFactoryInterface $formFactory)
    {
        $this->postManager    = $postManager;
        $this->commentManager = $commentManager;
        $this->mailer         = $mailer;
        $this->formFactory    = $formFactory;
    }

    /**
     * Retrieves the list of posts (paginated) based on criteria
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\NewsBundle\Model\Post", "groups"="sonata_api_read"}
     * )
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for posts list pagination")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of posts by page")
     * @QueryParam(name="enabled", requirements="0|1", nullable=true, strict=true, description="Enabled/Disabled posts filter")
     * @QueryParam(name="dateQuery", requirements=">|<|=", default=">", description="Date filter orientation (>, < or =)")
     * @QueryParam(name="dateValue", requirements="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-2][0-9]:[0-5][0-9]:[0-5][0-9]([+-][0-9]{2}(:)?[0-9]{2})?", nullable=true, strict=true, description="Date filter value")
     * @QueryParam(name="tag", requirements="\s", nullable=true, strict=true, description="Tag name filter")
     * @QueryParam(name="author", requirements="\s", nullable=true, strict=true, description="Author filter")
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Post[]
     */
    public function getPostsAction(ParamFetcherInterface $paramFetcher)
    {
        $page  = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        /** @var Pager $postsPager */
        $postsPager = $this->postManager->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $postsPager->getResults();
    }

    /**
     * Retrieves a specific post
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="post id"}
     *  },
     *  output={"class"="Sonata\NewsBundle\Model\Post", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when post is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Post
     */
    public function getPostAction($id)
    {
        return $this->getPost($id);
    }

    /**
     * Retrieves the comments of specified post
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="post id"}
     *  },
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when post is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Comment[]
     */
    public function getPostCommentsAction($id)
    {
        return $this->getPost($id)->getComments();
    }

    /**
     * Adds a comment to a post
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="post id"}
     *  },
     *  input={
     *      "class"="Sonata\NewsBundle\Form\Type\CommentType",
     *      "name"="comment",
     *  },
     *  output="Sonata\NewsBundle\Model\Comment",
     *  statusCodes={
     *      200="Returned when successful",
     *      403="Returned when invalid parameters",
     *      404="Returned when post is not found"
     *  }
     * )
     *
     * @param int     $id Post id
     * @param Request $request
     *
     * @return Comment|FormInterface
     * @throws HttpException
     */
    public function postPostCommentsAction($id, Request $request)
    {
        $post = $this->getPost($id);

        if (!$post->isCommentable()) {
            throw new HttpException(403, sprintf('Post (%d) not commentable', $id));
        }

        $comment = $this->commentManager->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        $form = $this->formFactory->createNamed('comment', 'sonata_post_comment', $comment, array('csrf_protection' => false));
        $form->bind($request);

        if ($form->isValid()) {
            $comment = $form->getData();

            $this->commentManager->save($comment);
            $this->mailer->sendCommentNotification($comment);

            $view = \FOS\RestBundle\View\View::create($comment);
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(array('sonata_api_read'));
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);

            return $view;
        }

        return $form;
    }

    /**
     * Filters criteria from $paramFetcher to be compatible with the Pager criteria
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return array The filtered criteria
     */
    protected function filterCriteria(ParamFetcherInterface $paramFetcher)
    {
        $criteria = $paramFetcher->all();

        unset($criteria['page'], $criteria['count']);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        if (array_key_exists('dateValue', $criteria)) {
            $date = new \DateTime($criteria['dateValue']);
            $criteria['date'] = array(
                'query'  => sprintf('p.publicationDateStart %s :dateValue', $criteria['dateQuery']),
                'params' => array('dateValue' => $date)
            );
            unset($criteria['dateValue'], $criteria['dateQuery']);
        } else {
            unset($criteria['dateQuery']);
        }

        return $criteria;
    }

    /**
     * Retrieves post with id $id or throws an exception if it doesn't exist
     *
     * @param $id
     *
     * @return Post
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPost($id)
    {
        $post = $this->postManager->findOneBy(array('id' => $id));

        if (null === $post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        return $post;
    }
}