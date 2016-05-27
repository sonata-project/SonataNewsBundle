<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentAdminController extends CRUDController
{
    /**
     * @param ProxyQueryInterface $query
     *
     * @return RedirectResponse
     */
    public function batchActionEnabled(ProxyQueryInterface $query)
    {
        return $this->commentChangeStatus($query, true);
    }

    /**
     * @param ProxyQueryInterface $query
     *
     * @return RedirectResponse
     */
    public function batchActionDisabled(ProxyQueryInterface $query)
    {
        return $this->commentChangeStatus($query, false);
    }

    /**
     * @param ProxyQueryInterface $query
     * @param                     $status
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     */
    protected function commentChangeStatus(ProxyQueryInterface $query, $status)
    {
        if (false === $this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        foreach ($query->execute() as $comment) {
            $comment->setStatus($status);

            $this->admin->getModelManager()->update($comment);
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}
