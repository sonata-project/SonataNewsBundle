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

namespace Sonata\NewsBundle\Model;

abstract class Comment implements CommentInterface
{
    /**
     * Name of the author.
     *
     * @var string|null
     */
    protected $name;

    /**
     * Email of the author.
     *
     * @var string|null
     */
    protected $email;

    /**
     * Website url of the author.
     *
     * @var string|null
     */
    protected $url;

    /**
     * Comment content.
     *
     * @var string
     */
    protected $message;

    /**
     * Comment created date.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update date.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Moderation status.
     *
     * @var int
     */
    protected $status = self::STATUS_VALID;

    /**
     * Post for which the comment is related to.
     *
     * @var PostInterface
     */
    protected $post;

    public function __toString()
    {
        return $this->getName() ?: 'n-a';
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUrl($url): void
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_MODERATE => 'moderate',
            self::STATUS_INVALID => 'invalid',
            self::STATUS_VALID => 'valid',
        ];
    }

    public function getStatusCode()
    {
        $status = self::getStatusList();

        return isset($status[$this->getStatus()]) ? $status[$this->getStatus()] : null;
    }

    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setPost($post): void
    {
        $this->post = $post;
    }

    public function getPost()
    {
        return $this->post;
    }
}
