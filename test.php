<?php

use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\NewsBundle\Admin\CommentAdmin;

require 'vendor/autoload.php';

class Test extends CommentAdmin
{
    public function setCommentManager(ManagerInterface $commentManager)
    {
    }
}
