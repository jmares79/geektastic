<?php

namespace ReportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ReportCommand extends ContainerAwareCommand
{
    /**
     * Configures the command, setting name, arguments and description
     *
     * @return void Set a new command for creating a report
     */
    protected function configure()
    {
       $this
        ->setName('app:calc-total')
        ->setDescription('Calculates the total of the loaded products.')
        ->setHelp('This command allows you to create a report for printing product totals.');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input Symfony input component for enter data to command
     * @param OutputInterface $input Symfony output component for putting data to stdout
     *
     * @return void Executes all neccesary steps for creating a report
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reportService = $this->getContainer()->get('report_service');

        $reportService->createReport();
        $reportService->printReport();
    }
}
