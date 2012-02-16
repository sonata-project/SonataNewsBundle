<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use Sonata\NewsBundle\Model\CommentInterface;

abstract class Comment implements CommentInterface
{
    protected $name;

    protected $email;

    protected $url;

    protected $message;

    protected $createdAt;

    protected $updatedAt;

    protected $status = self::STATUS_VALID;

    protected $post;

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set url
     *
     * @param text $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return text $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set message
     *
     * @param text $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return text $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public static function getStatusList()
    {
        return array(
            self::STATUS_MODERATE => 'moderate',
            self::STATUS_INVALID => 'invalid',
            self::STATUS_VALID   => 'valid',

        );
    }

    public function getStatusCode()
    {
        $status = self::getStatusList();

        return isset($status[$this->getStatus()]) ? $status[$this->getStatus()] : null;
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    public function postCreate()
    {
        if ($this->getStatus() == self::STATUS_VALID) {
            $this->getPost()->setCommentsCount($this->getPost()->getCommentsCount()+1);
        }
    }

    public function postDelete()
    {
        if ($this->getPost()->getCommentsCount() > 0 && $this->getPost()) {
            $this->getPost()->setCommentsCount($this->getPost()->getCommentsCount()-1);
        }

    }

    public function postUpdate()
    {
       if ($this->getOldStatus() != self::STATUS_VALID && $this->getStatus() == self::STATUS_VALID) {
           $this->getPost()->setCommentsCount($this->getPost()->getCommentsCount()+1);
       }
       elseif ($this->getOldStatus() == self::STATUS_VALID && $this->getStatus() != self::STATUS_VALID) {
           $this->getPost()->setCommentsCount($this->getPost()->getCommentsCount()-1);
       }
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set post
     *
     * @param Application\Sonata\NewsBundle\Model\PostInterface $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * Get post
     *
     * @return Application\Sonata\NewsBundle\Model\PostInterface $post
     */
    public function getPost()
    {
        return $this->post;
    }

    public function __toString()
    {
        return $this->getName() ?: 'n-a';
    }
}