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

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Route;

use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sonata\NewsBundle\Model\Comment;
use Sonata\NewsBundle\Model\CommentManagerInterface;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CommentController
 *
 * @package Sonata\NewsBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CommentController
{
    /**
     * @var CommentManagerInterface
     */
    protected $commentManager;

    /**
     * Constructor
     *
     * @param CommentManagerInterface $commentManager A comment manager
     */
    public function __construct(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Retrieves a specific comment
     *
     * @ApiDoc(
     *  resource=true,
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="comment id"}
     *  },
     *  output={"class"="Sonata\NewsBundle\Model\Comment", "groups"="sonata_api_read"},
     *  statusCodes={
     *      200="Returned when successful",
     *      404="Returned when comment is not found"
     *  }
     * )
     *
     * @View(serializerGroups="sonata_api_read", serializerEnableMaxDepthChecks=true)
     *
     * @Route(requirements={"_format"="json|xml"})
     *
     * @param integer $id A comment identifier
     *
     * @return Comment
     * @throws NotFoundHttpException
     */
    public function getCommentAction($id)
    {
        return $this->getComment($id);
    }

    /**
     * Deletes a comment
     *
     * @ApiDoc(
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="comment identifier"}
     *  },
     *  statusCodes={
     *      200="Returned when comment is successfully deleted",
     *      400="Returned when an error has occurred while comment deletion",
     *      404="Returned when unable to find comment"
     *  }
     * )
     *
     * @Route(requirements={"_format"="json|xml"})
     *
     * @param integer $id A comment identifier
     *
     * @return \FOS\RestBundle\View\View
     *
     * @throws NotFoundHttpException
     */
    public function deleteCommentAction($id)
    {
        $comment = $this->getComment($id);

        try {
            $this->commentManager->delete($comment);
        } catch (\Exception $e) {
            return \FOS\RestBundle\View\View::create(array('error' => $e->getMessage()), 400);
        }

        return array('deleted' => true);
    }

    /**
     * Returns a comment entity instance
     *
     * @param integer $id A comment identifier
     *
     * @return Comment
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getComment($id)
    {
        $comment = $this->commentManager->find($id);

        if (null === $comment) {
            throw new NotFoundHttpException(sprintf("Comment (%d) not found", $id));
        }

        return $comment;
    }
}