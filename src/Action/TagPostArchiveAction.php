<?php

declare(strict_types=1);

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
use Symfony\Component\Translation\TranslatorInterface;

final class TagPostArchiveAction extends AbstractPostArchiveAction
{
    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    public function __construct(
        BlogInterface $blog,
        PostManagerInterface $postManager,
        TranslatorInterface $translator,
        TagManagerInterface $tagManager
    ) {
        parent::__construct($blog, $postManager, $translator);

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

        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                ->addTitle($this->trans('archive_tag.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%tag%' => $tag->getName(),
                ]))
                ->addMeta('property', 'og:title', $this->trans('archive_tag.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%tag%' => $tag->getName(),
                ]))
                ->addMeta('name', 'description', $this->trans('archive_tag.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%tag%' => $tag->getName(),
                ]))
                ->addMeta('property', 'og:description', $this->trans('archive_tag.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%tag%' => $tag->getName(),
                ]));
        }

        return $this->renderArchive($request, ['tag' => $tag->getSlug()], ['tag' => $tag]);
    }
}
