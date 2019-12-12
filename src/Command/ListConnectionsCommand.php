<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Database\Database;
use SychO\Populater\Command\AddConnectionCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Codedungeon\PHPCliColors\Color;

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
            $connections = Yaml::parseFile(AddConnectionCommand::INPUT_FILE);
        } catch (ParseException $e) {
            $output->writeln('Cannot read connections.yml');
        }

        foreach ($connections as $name => $vars) {
            $output->writeln(Color::RED . 'Connection: ' . Color::LIGHT_GREEN . $name);

            foreach ($vars as $var => $value) {
                $arg = Database::ENV[$var]['name'];

                if ($arg !== 'connection_name')
                    $output->writeln(Color::CYAN . "\t$arg: " . Color::LIGHT_GREEN . $value);
            }
        }

        return 0;
    }
}
