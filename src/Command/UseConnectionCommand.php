<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Command\AddConnectionCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Codedungeon\PHPCliColors\Color;

class UseConnectionCommand extends Command
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->filesystem = new Filesystem();
    }

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
            $connections = Yaml::parseFile(AddConnectionCommand::INPUT_FILE);
        } catch (ParseException $e) {
            $output->writeln('Could not get the list of available connections. Failed to parse connections.yml');
        }

        $connection_name = $input->getArgument('connection_name');

        if (empty($connections[$connection_name])) {
            $output->writeln("Found no connection by the name of '$connection_name' try adding the connection first using");
            $output->writeln(Color::RED . "php populater add:connection");

            return 0;
        }

        foreach ($connections[$connection_name] as $var => $value)
            $env[] = "$var=$value";

        // Write to .env
        $this->filesystem->dumpFile(__DIR__ . '/../../.env', implode("\n", $env));

        return 0;
    }
}
