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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PostArchiveAction extends AbstractPostArchiveAction
{
    /**
     * @return Response
     */
    public function __invoke(Request $request)
    {
        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                ->addTitle($this->trans('archive.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                ]))
                ->addMeta('property', 'og:title', $this->trans('archive.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                ]))
                ->addMeta('name', 'description', $this->trans('archive.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%description%' => $this->getBlog()->getDescription(),
                ]))
                ->addMeta('property', 'og:description', $this->trans('archive.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%description%' => $this->getBlog()->getDescription(),
                ]));
        }

        return $this->renderArchive($request);
    }
}
