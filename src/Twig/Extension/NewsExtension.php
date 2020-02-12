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

namespace Sonata\NewsBundle\Twig\Extension;

use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\NewsBundle\Model\BlogInterface;
use Sonata\NewsBundle\Model\PostInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\InitRuntimeInterface;
use Twig\TwigFunction;

class NewsExtension extends AbstractExtension implements InitRuntimeInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var BlogInterface
     */
    private $blog;

    public function __construct(RouterInterface $router, ManagerInterface $tagManager, BlogInterface $blog)
    {
        if (!$tagManager instanceof TagManagerInterface) {
            @trigger_error(
                'Calling the '.__METHOD__.' method with a Sonata\Doctrine\Model\ManagerInterface is deprecated'
                .' since version 2.4 and will be removed in 4.0.'
                .' Use the new signature with a Sonata\ClassificationBundle\Model\TagManagerInterface instead.',
                E_USER_DEPRECATED
            );
        }

        $this->router = $router;
        $this->tagManager = $tagManager;
        $this->blog = $blog;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'sonata_news_link_tag_rss',
                [$this, 'renderTagRss', ['is_safe' => ['html']]]
            ),
            new TwigFunction(
                'sonata_news_permalink',
                [$this, 'generatePermalink']
            ),
        ];
    }

    public function initRuntime(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sonata_news';
    }

    /**
     * @return string
     */
    public function renderTagRss()
    {
        $rss = [];
        foreach ($this->tagManager->findBy(['enabled' => true]) as $tag) {
            $rss[] = sprintf(
                '<link href="%s" title="%s : %s" type="application/rss+xml" rel="alternate" />',
                $this->router->generate('sonata_news_tag', ['tag' => $tag->getSlug(), '_format' => 'rss'], UrlGeneratorInterface::ABSOLUTE_URL),
                $this->blog->getTitle(),
                $tag->getName()
            );
        }

        return implode("\n", $rss);
    }

    /**
     * @return string
     */
    public function generatePermalink(PostInterface $post)
    {
        return $this->blog->getPermalinkGenerator()->generate($post);
    }
}
