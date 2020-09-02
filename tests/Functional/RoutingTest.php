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

namespace Sonata\NewsBundle\Tests\Functional\Routing;

use Nelmio\ApiDocBundle\Annotation\Operation;
use Sonata\NewsBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class RoutingTest extends WebTestCase
{
    /**
     * @group legacy
     *
     * @dataProvider getRoutes
     */
    public function testRoutes(string $name, string $path, array $methods): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        $route = $router->getRouteCollection()->get($name);

        $this->assertNotNull($route);
        $this->assertSame($path, $route->getPath());
        $this->assertEmpty(array_diff($methods, $route->getMethods()));

        $matchingPath = $path;
        $matchingFormat = '';
        if (\strlen($matchingPath) >= 10 && false !== strpos($matchingPath, '.{_format}', -10)) {
            $matchingFormat = '.json';
            $matchingPath = str_replace('.{_format}', $matchingFormat, $path);
        }

        $matcher = $router->getMatcher();
        $requestContext = $router->getContext();

        foreach ($methods as $method) {
            $requestContext->setMethod($method);

            // Check paths like "/api/news/posts.json".
            $match = $matcher->match($matchingPath);

            $this->assertSame($name, $match['_route']);

            if ($matchingFormat) {
                $this->assertSame(ltrim($matchingFormat, '.'), $match['_format']);
            }

            $matchingPathWithStrippedFormat = str_replace('.{_format}', '', $path);

            // Check paths like "/api/news/posts".
            $match = $matcher->match($matchingPathWithStrippedFormat);

            $this->assertSame($name, $match['_route']);

            if ($matchingFormat) {
                $this->assertSame(ltrim($matchingFormat, '.'), $match['_format']);
            }
        }
    }

    public function getRoutes(): iterable
    {
        // API
        if (class_exists(Operation::class)) {
            yield ['app.swagger_ui', '/api/doc', ['GET']];
            yield ['app.swagger', '/api/doc.json', ['GET']];
        } else {
            yield ['nelmio_api_doc_index', '/api/doc/{view}', ['GET']];
        }
        // API - comment
        yield ['sonata_api_news_comment_get_comment', '/api/news/comments/{id}.{_format}', ['GET']];
        yield ['sonata_api_news_comment_delete_comment', '/api/news/comments/{id}.{_format}', ['DELETE']];

        // API - post
        yield ['sonata_api_news_post_get_posts', '/api/news/posts.{_format}', ['GET']];
        yield ['sonata_api_news_post_get_post', '/api/news/posts/{id}.{_format}', ['GET']];
        yield ['sonata_api_news_post_post_post', '/api/news/posts.{_format}', ['POST']];
        yield ['sonata_api_news_post_put_post', '/api/news/posts/{id}.{_format}', ['PUT']];
        yield ['sonata_api_news_post_delete_post', '/api/news/posts/{id}.{_format}', ['DELETE']];
        yield ['sonata_api_news_post_get_post_comments', '/api/news/posts/{id}/comments.{_format}', ['GET']];
        yield ['sonata_api_news_post_post_post_comments', '/api/news/posts/{id}/comments.{_format}', ['POST']];
        yield ['sonata_api_news_post_put_post_comments', '/api/news/posts/{postId}/comments/{commentId}.{_format}', ['PUT']];
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
