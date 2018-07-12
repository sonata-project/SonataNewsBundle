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

namespace Sonata\Tests\Action;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Sonata\NewsBundle\Action\DailyPostArchiveAction;
use Sonata\NewsBundle\Entity\PostManager;
use Sonata\NewsBundle\Model\BlogInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class DailyPostArchiveActionTest extends TestCase
{
    public function testInvoke(): void
    {
        $blog = $this->prophesize(BlogInterface::class);
        $translator = $this->prophesize(TranslatorInterface::class);
        $dateTimeHelper = $this->prophesize(DateTimeHelper::class);

        $dataParams = [
            'query' => 'foo.publicationDateStart >= :startDate AND bar.publicationDateStart < :endDate',
            'params' => [
                'startDate' => new \DateTime(),
                'endDate' => new \DateTime('tomorrow'),
            ],
        ];

        $postManager = $this->prophesize(PostManager::class);
        $postManager->getPublicationDateQueryParts('2018-7-8', 'day')
            ->willReturn($dataParams);
        $postManager->getPager([
            'date' => $dataParams,
        ], 1)
            ->willReturn($this->prophesize(PagerInterface::class));

        $twig = $this->prophesize(Environment::class);
        $twig->render('@SonataNews/Post/archive.html.twig', Argument::any())
            ->willReturn('HTML CONTENT');

        $container = new Container();
        $container->set('twig', $twig->reveal());

        $action = new DailyPostArchiveAction(
            $blog->reveal(),
            $postManager->reveal(),
            $translator->reveal(),
            $dateTimeHelper->reveal()
        );
        $action->setContainer($container);

        $request = new Request();
        $request->query->set('page', 1);

        $response = $action($request, 2018, 7, 8);

        $this->assertInstanceOf(Response::class, $response);
    }
}
