<?php

namespace Commands;

use Saxulum\Console\Command\AbstractPimpleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestFatalCommand extends AbstractPimpleCommand
{
    protected function configure()
    {
        $this
            ->setName('test:fatal');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $null = null;
        $null->fatal();
    }
}
