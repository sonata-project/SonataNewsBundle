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

use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MonthlyPostArchiveAction extends AbstractPostArchiveAction
{
    /**
     * @var DateTimeHelper
     */
    private $dateTimeHelper;

    /**
     * NEXT_MAJOR: Remove usage of LegacyTranslator.
     *
     * @param TranslatorInterface|LegacyTranslator $translator
     */
    public function __construct(
        BlogInterface $blog,
        PostManagerInterface $postManager,
        object $translator,
        DateTimeHelper $dateTimeHelper
    ) {
        parent::__construct($blog, $postManager, $translator);

        $this->dateTimeHelper = $dateTimeHelper;
    }

    /**
     * @param string $year
     * @param string $month
     *
     * @return Response
     */
    public function __invoke(Request $request, $year, $month)
    {
        $date = $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, $month, 1), 'month');

        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                ->addTitle($this->trans('archive_month.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                    '%month%' => $this->dateTimeHelper->format($date, 'MMMM'),
                ]))
                ->addMeta('property', 'og:title', $this->trans('archive_month.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                    '%month%' => $this->dateTimeHelper->format($date, 'MMMM'),
                ]))
                ->addMeta('name', 'description', $this->trans('archive_month.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                    '%month%' => $this->dateTimeHelper->format($date, 'MMMM'),
                    '%description%' => $this->getBlog()->getDescription(),
                ]))
                ->addMeta('property', 'og:description', $this->trans('archive_month.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%year%' => $year,
                    '%month%' => $this->dateTimeHelper->format($date, 'MMMM'),
                    '%description%' => $this->getBlog()->getDescription(),
                ]));
        }

        return $this->renderArchive($request, [
            'date' => $date,
        ], []);
    }
}
