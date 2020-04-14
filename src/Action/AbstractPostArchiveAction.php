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

use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractPostArchiveAction extends Controller
{
    /**
     * @var BlogInterface
     */
    private $blog;

    /**
     * @var PostManagerInterface
     */
    private $postManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SeoPageInterface|null
     */
    private $seoPage;

    public function __construct(BlogInterface $blog, PostManagerInterface $postManager, TranslatorInterface $translator)
    {
        $this->blog = $blog;
        $this->postManager = $postManager;
        $this->translator = $translator;
    }

    /**
     * @internal
     *
     * NEXT_MAJOR: make this method protected
     *
     * @return Response
     */
    final public function renderArchive(Request $request, array $criteria = [], array $parameters = [])
    {
        $pager = $this->postManager->getPager(
            $criteria,
            $request->get('page', 1)
        );

        $parameters = array_merge([
            'pager' => $pager,
            'blog' => $this->blog,
            'tag' => false,
            'collection' => false,
            'route' => $request->get('_route'),
            'route_parameters' => $request->get('_route_params'),
        ], $parameters);

        $response = $this->render(
            sprintf('@SonataNews/Post/archive.%s.twig', $request->getRequestFormat()),
            $parameters
        );

        if ('rss' === $request->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        return $response;
    }

    public function setSeoPage(?SeoPageInterface $seoPage = null)
    {
        $this->seoPage = $seoPage;
    }

    /**
     * @param string      $id
     * @param string|null $domain
     * @param string|null $locale
     *
     * @return string
     */
    final protected function trans($id, array $parameters = [], $domain = 'SonataNewsBundle', $locale = null)
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @return BlogInterface
     */
    final protected function getBlog()
    {
        return $this->blog;
    }

    /**
     * @return SeoPageInterface|null
     */
    final protected function getSeoPage()
    {
        return $this->seoPage;
    }

    /**
     * @return PostManagerInterface
     */
    final protected function getPostManager()
    {
        return $this->postManager;
    }
}
