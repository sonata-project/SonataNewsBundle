<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Action;

use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TagPostArchiveAction extends AbstractPostArchiveAction
{
    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    public function __construct(BlogInterface $blog, PostManagerInterface $postManager, TagManagerInterface $tagManager)
    {
        parent::__construct($blog, $postManager);

        $this->tagManager = $tagManager;
    }

    /**
     * @param string $tag
     *
     * @return Response
     */
    public function __invoke(Request $request, $tag)
    {
        $tag = $this->tagManager->findOneBy([
            'slug' => $tag,
            'enabled' => true,
        ]);

        if (!$tag || !$tag->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the tag');
        }

        return $this->renderArchive($request, ['tag' => $tag->getSlug()], ['tag' => $tag]);
    }
}
