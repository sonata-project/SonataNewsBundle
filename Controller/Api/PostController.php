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

use Application\Sonata\NewsBundle\Entity\Post;

use Sonata\AdminBundle\Datagrid\Pager;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Mailer\MailerInterface;
use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;
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
     * @var CommentManagerInterface
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
     * @var FormatterPool
     */
    protected $formatterPool;

    /**
     * Constructor
     *
     * @param PostManagerInterface    $postManager
     * @param CommentManagerInterface $commentManager
     * @param MailerInterface         $mailer
     * @param FormFactoryInterface    $formFactory
     * @param FormatterPool           $formatterPool
     */
    public function __construct(PostManagerInterface $postManager, CommentManagerInterface $commentManager, MailerInterface $mailer, FormFactoryInterface $formFactory, FormatterPool $formatterPool)
    {
        $this->postManager    = $postManager;
        $this->commentManager = $commentManager;
        $this->mailer         = $mailer;
        $this->formFactory    = $formFactory;
        $this->formatterPool  = $formatterPool;
    }

    /**
     * Retrieves the list of posts (paginated) based on criteria
     *
     * @ApiDoc(
     *  resource=true,
     *  output={"class"="Sonata\NewsBundle\Model\Post", "groups"={"sonata_api_read"}}
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
     *  output={"class"="Sonata\NewsBundle\Model\Post", "groups"={"sonata_api_read"}},
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
     * Adds a post
     *
     * @ApiDoc(
     *  input={"class"="sonata_news_api_form_post", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\NewsBundle\Model\Post", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while post creation",
     *      404="Returned when unable to find post"
     *  }
     * )
     *
     * @param Request $request A Symfony request
     *
     * @return Post
     *
     * @throws NotFoundHttpException
     */
    public function postPostAction(Request $request)
    {
        return $this->handleWritePost($request);
    }

    /**
     * Updates a post
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="post identifier"}
     *  },
     *  input={"class"="sonata_news_api_form_post", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\NewsBundle\Model\Post", "groups"={"sonata_api_read"}},
     *  statusCodes={
     *      200="Returned when successful",
     *      400="Returned when an error has occurred while post update",
     *      404="Returned when unable to find post"
     *  }
     * )
     *
     * @param integer $id      A Post identifier
     * @param Request $request A Symfony request
     *
     * @return Post
     *
     * @throws NotFoundHttpException
     */
    public function putPostAction($id, Request $request)
    {
        return $this->handleWritePost($request, $id);
    }

    /**
     * Deletes a post
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="post identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when post is successfully deleted",
     *      400="Returned when an error has occurred while post deletion",
     *      404="Returned when unable to find post"
     *  }
     * )
     *
     * @param integer $id A Post identifier
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws NotFoundHttpException
     */
    public function deletePostAction($id)
    {
        $post = $this->getPost($id);

        try {
            $this->postManager->delete($post);
        } catch (\Exception $e) {
            return \FOS\RestBundle\View\View::create(array('error' => $e->getMessage()), 400);
        }

        return array('deleted' => true);
    }

    /**
     * Retrieves the comments of specified post
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="post id"}
     *  },
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"={"sonata_api_read"}},
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
     *  input={"class"="sonata_news_api_form_comment", "name"="", "groups"={"sonata_api_write"}},
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"={"sonata_api_read"}},
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
     *
     * @throws HttpException
     */
    public function postPostCommentsAction($id, Request $request)
    {
        $post = $this->getPost($id);

        if (!$post->isCommentable()) {
            throw new HttpException(403, sprintf('Post (%d) not commentable', $id));
        }

        $comment = $this->commentManager->create();

        $form = $this->formFactory->createNamed(null, 'sonata_news_api_form_comment', $comment, array('csrf_protection' => false));
        $form->bind($request);

        if ($form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);

            if (!$comment->getStatus()) {
                $comment->setStatus($post->getCommentsDefaultStatus());
            }

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
     * @param integer $id A Post identifier
     *
     * @return Post
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getPost($id)
    {
        $post = $this->postManager->find($id);

        if (null === $post) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        return $post;
    }

    /**
     * Write a post, this method is used by both POST and PUT action methods
     *
     * @param Request      $request Symfony request
     * @param integer|null $id      A post identifier
     *
     * @return \FOS\RestBundle\View\View|FormInterface
     */
    protected function handleWritePost($request, $id = null)
    {
        $post = $id ? $this->getPost($id) : null;

        $form = $this->formFactory->createNamed(null, 'sonata_news_api_form_post', $post, array(
            'csrf_protection' => false
        ));

        $form->bind($request);

        if ($form->isValid()) {
            $post = $form->getData();
            $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
            $this->postManager->save($post);

            $view = \FOS\RestBundle\View\View::create($post);
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups(array('sonata_api_read'));
            $serializationContext->enableMaxDepthChecks();
            $view->setSerializationContext($serializationContext);

            return $view;
        }

        return $form;
    }
}