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

namespace Sonata\NewsBundle\Tests\Functional;

use Sonata\NewsBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class RoutingTest extends WebTestCase
{
    /**
     * @dataProvider getRoutes
     */
    public function testRoutes(string $name, string $path, array $methods, ?array $formats): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        $route = $router->getRouteCollection()->get($name);

        static::assertNotNull($route);
        static::assertSame($path, $route->getPath());
        static::assertEmpty(array_diff($methods, $route->getMethods()));

        if ($formats) {
            static::assertEmpty(array_diff($formats, explode('|', $route->getRequirement('_format') ?: '')));
        }
    }

    public function getRoutes(): iterable
    {
        //yield [string $name, string $path, array $methods, ?array $formats];
        yield ['sonata_news_home', '/', ['GET'], null];
        yield ['sonata_news_add_comment', '/add-comment/{id}', ['POST'], null];
        yield ['sonata_news_archive', '/archive.{_format}', ['GET'], ['html', 'rss']];
        yield ['sonata_news_archive_daily', '/archive/{year}/{month}/{day}.{_format}', ['GET'], ['html', 'rss']];
        yield ['sonata_news_archive_monthly', '/archive/{year}/{month}.{_format}', ['GET'], ['html', 'rss']];
        yield ['sonata_news_archive_yearly', '/archive/{year}.{_format}', ['GET'], ['html', 'rss']];
        yield ['sonata_news_collection', '/collection/{collection}.{_format}', ['GET'], ['html', 'rss']];
        yield ['sonata_news_comment_moderation', '/comment/moderation/{commentId}/{hash}/{status}', ['GET'], null];
        yield ['sonata_news_tag', '/tag/{tag}.{_format}', ['GET'], ['html', 'rss']];
        yield ['sonata_news_view', '/{permalink}.{_format}', ['GET'], ['html', 'rss']];
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
