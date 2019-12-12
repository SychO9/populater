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

class UseConnectionCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setDescription('Changes the current connection')
            ->setHelp('Changes the current connection to one of the added connections');

        $this->addArgument('connection_name', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = $env = [];

        try {
            $connections = StorageManager::read('connections.yml');
        } catch (FileReadException $e) {
            $output->writeln("<fg=yellow>{$e->getMessage()}</>");
            return 0;
        }

        $connection_name = $input->getArgument('connection_name');

        if (empty($connections[$connection_name])) {
            $output->writeln("Found no connection by the name of '$connection_name' try adding the connection first using");
            $output->writeln("<fg=red>php populater add:connection</>");

            return 0;
        }

        // Write to .env
        StorageManager::writeToEnv($connections[$connection_name]);

        return 0;
    }
}
