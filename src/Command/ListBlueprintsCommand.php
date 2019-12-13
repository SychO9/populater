<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\StorageManager;
use SychO\Populater\Exception\FileReadException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListBlueprintsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Lists existing blueprints');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $blueprints = StorageManager::getBlueprints();
        $connections = StorageManager::read('connections.yml');
        $connections['common'] = 'common';
        $blueprint_groups = [];

        foreach ($connections as $connection_name => $conn) {
            foreach ($blueprints as $name => $file) {
                if (empty($file->getRelativePath()))
                    $path = 'common';
                else
                    $path = $file->getRelativePath();

                if ($path !== $connection_name)
                    continue;

                $blueprint_groups[$connection_name][] = $name;
            }
        }

        foreach ($blueprint_groups as $connection_name => $blueprints) {
            $output->writeln("<info>$connection_name</info>:");

            foreach ($blueprints as $bp) {
                $output->writeln("\t- <fg=red>$bp</>");
            }
        }

        return 0;
    }
}
