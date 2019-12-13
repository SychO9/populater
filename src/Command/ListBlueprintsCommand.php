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
        $connections['common'] = ['DB_NAME' => 'common'];
        $blueprint_groups = [];

        foreach ($connections as $connection_name => $conn_info) {
            foreach ($blueprints as $name => $file) {
                if (empty($file->getRelativePath()))
                    $path = 'common';
                else
                    $path = $file->getRelativePath();

                if ($path !== $conn_info['DB_NAME'])
                    continue;

                $blueprint_groups[$conn_info['DB_NAME']][] = $name;
            }
        }

        foreach ($blueprint_groups as $database => $blueprints) {
            $output->writeln("<info>$database</info>:");

            foreach ($blueprints as $bp) {
                $output->writeln("    - <fg=red>$bp</>");
            }
        }

        return 0;
    }
}
