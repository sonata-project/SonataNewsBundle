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
        return $this->renderArchive($request, [
            'date' => $this->getPostManager()->getPublicationDateQueryParts(sprintf('%d-%d-%d', $year, 1, 1), 'year'),
        ], []);
    }
}
