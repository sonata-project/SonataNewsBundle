<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\ClassLoader\ClassCollectionLoader;

use Sonata\NewsBundle\Model\Comment;

class SynchronizeCommentsCountCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('sonata:news:sync-comments-count');
        $this->setDescription('Synchronize comments count');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $commentManager = $this->getContainer()->get('sonata.news.manager.comment');

        $commentManager->updateCommentsCount();

        $output->writeln(" done!");
    }
}