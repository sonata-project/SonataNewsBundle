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

final class YearlyPostArchiveAction extends AbstractPostArchiveAction
{
    /**
     * @param string $year
     *
     * @return Response
     */
    public function __invoke(Request $request, $year)
    {
        $date = $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year');

        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                ->addTitle($this->trans('archive_year.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                ]))
                ->addMeta('property', 'og:title', $this->trans('archive_year.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                ]))
                ->addMeta('name', 'description', $this->trans('archive_year.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                    '%description%' => $this->getBlog()->getDescription(),
                ]))
                ->addMeta('property', 'og:description', $this->trans('archive_year.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                    '%description%' => $this->getBlog()->getDescription(),
                ]));
        }

        return $this->renderArchive($request, [
            'date' => $date,
        ], []);
    }
}
