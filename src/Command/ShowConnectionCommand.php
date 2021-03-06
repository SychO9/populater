<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Database\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowConnectionCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Shows info on the current connection');

        $this->addOption('full');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<fg=red>Connection:</> <fg=green>'.env('CONNECTION').'</>');

        if ($input->getOption('full')) {
            foreach (Database::ENV as $var => $info) {
                if ($info['name'] !== 'connection_name')
                    $output->writeln("<fg=blue>\t{$info['name']}:</> <fg=green>".env($var, $info['default'])."</>");
            }
        }

        return 0;
    }
}
