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
use Sonata\NewsBundle\Model\PostInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ViewPostAction extends Controller
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
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var SeoPageInterface|null
     */
    private $seoPage;

    public function __construct(
        BlogInterface $blog,
        PostManagerInterface $postManager,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->blog = $blog;
        $this->postManager = $postManager;
        $this->authChecker = $authChecker;
    }

    /**
     * @param string $permalink
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function __invoke($permalink)
    {
        $post = $this->postManager->findOneByPermalink($permalink, $this->blog);

        if (!$post || !$this->isVisible($post)) {
            throw new NotFoundHttpException('Unable to find the post');
        }

        if ($seoPage = $this->seoPage) {
            $seoPage
                ->addTitle($post->getTitle())
                ->addMeta('name', 'description', $post->getAbstract())
                ->addMeta('property', 'og:title', $post->getTitle())
                ->addMeta('property', 'og:type', 'blog')
                ->addMeta('property', 'og:url', $this->generateUrl('sonata_news_view', [
                    'permalink' => $this->blog->getPermalinkGenerator()->generate($post),
                ], UrlGeneratorInterface::ABSOLUTE_URL))
                ->addMeta('property', 'og:description', $post->getAbstract())
            ;
        }

        return $this->render('@SonataNews/Post/view.html.twig', [
            'post' => $post,
            'form' => false,
            'blog' => $this->blog,
        ]);
    }

    public function setSeoPage(?SeoPageInterface $seoPage = null)
    {
        $this->seoPage = $seoPage;
    }

    /**
     * @return bool
     */
    protected function isVisible(PostInterface $post)
    {
        return $post->isPublic() ||
            $this->authChecker->isGranted('ROLE_SUPER_ADMIN') ||
            $this->authChecker->isGranted('ROLE_SONATA_NEWS_ADMIN_POST_EDIT');
    }
}
