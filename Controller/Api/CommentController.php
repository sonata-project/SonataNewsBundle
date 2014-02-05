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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sonata\NewsBundle\Model\Comment;

use Sonata\NewsBundle\Model\CommentManagerInterface;
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
     * @param CommentManagerInterface $commentManager
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
     * @param $id
     *
     * @return Comment
     * @throws NotFoundHttpException
     */
    public function getCommentAction($id)
    {
        $comment = $this->commentManager->findOneBy(array('id' => $id));

        if (null === $comment) {
            throw new NotFoundHttpException(sprintf("Comment (%d) not found", $id));
        }

        return $comment;
    }
}