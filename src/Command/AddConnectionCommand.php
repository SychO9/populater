<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Database\Database;
use SychO\Populater\Exception\FileReadException;
use SychO\Populater\StorageManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddConnectionCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Command info
        $this
            ->setDescription('Adds a new database connection')
            ->setHelp('This command adds a new database connection');

        // Arguments
        foreach (Database::ENV as $var => $info)
            $this->addArgument($info['name'], $info['required'] ? InputArgument::REQUIRED : InputArgument::OPTIONAL);
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

        foreach (Database::ENV as $var => $info) {
            $conn = $input->getArgument('connection_name');

            if ($info['name'] !== $conn)
                $connections[$conn][$var] = $input->getArgument($info['name']) ?? $info['default'];
        }

        StorageManager::writeArrayTo('connections.yml', $connections);
        $output->writeln('<fg=green>Connection added.</>');

        return 0;
    }
}
