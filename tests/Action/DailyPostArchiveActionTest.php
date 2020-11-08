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

namespace Sonata\NewsBundle\Tests\Action;

use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Sonata\NewsBundle\Action\DailyPostArchiveAction;
use Sonata\NewsBundle\Entity\PostManager;
use Sonata\NewsBundle\Model\BlogInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class DailyPostArchiveActionTest extends TestCase
{
    public function testInvoke(): void
    {
        $blog = $this->createStub(BlogInterface::class);
        $translator = $this->createStub(TranslatorInterface::class);
        $dateTimeHelper = $this->createStub(DateTimeHelper::class);

        $dataParams = [
            'query' => 'foo.publicationDateStart >= :startDate AND bar.publicationDateStart < :endDate',
            'params' => [
                'startDate' => new \DateTime(),
                'endDate' => new \DateTime('tomorrow'),
            ],
        ];

        $postManager = $this->createStub(PostManager::class);
        $postManager->method('getPublicationDateQueryParts')->with('2018-7-8', 'day')
            ->willReturn($dataParams);
        $postManager->method('getPager')->with(['date' => $dataParams], 1)
            ->willReturn($this->createStub(PagerInterface::class));

        $twig = $this->createStub(Environment::class);
        $twig->method('render')->with('@SonataNews/Post/archive.html.twig', $this->anything())
            ->willReturn('HTML CONTENT');

        $container = new Container();
        $container->set('twig', $twig);

        $action = new DailyPostArchiveAction(
            $blog,
            $postManager,
            $translator,
            $dateTimeHelper
        );
        $action->setContainer($container);

        $request = new Request();
        $request->query->set('page', 1);

        $response = $action($request, 2018, 7, 8);

        $this->assertInstanceOf(Response::class, $response);
    }
}
