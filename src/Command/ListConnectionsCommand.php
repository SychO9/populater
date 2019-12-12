<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Database\Database;
use SychO\Populater\StorageManager;
use SychO\Populater\Exception\FileReadException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListConnectionsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Lists all added connections');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = [];

        try {
            $connections = StorageManager::read('connections.yml');
        } catch (FileReadException $e) {
            $output->writeln("<fg=yellow>{$e->getMessage()}</>");
            return 0;
        }

        foreach ($connections as $name => $vars) {
            $output->writeln("<fg=red>Connection: </><fg=green>$name</>");

            foreach ($vars as $var => $value) {
                $arg = Database::ENV[$var]['name'];

                if ($arg !== 'connection_name')
                    $output->writeln("<fg=blue>\t$arg: </><fg=green>$value</>");
            }
        }

        return 0;
    }
}
