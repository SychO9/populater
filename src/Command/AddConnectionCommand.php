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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Codedungeon\PHPCliColors\Color;

class AddConnectionCommand extends Command
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $inputFile = __DIR__ . '/../../storage/connections.yml';

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
            $connections = Yaml::parseFile($this->inputFile);
        } catch (ParseException $e) {
            $output->writeln('Cannot read connections.yml');
        }

        foreach (Database::ENV as $var => $info) {
            $conn = $input->getArgument('connection_name');

            if ($info['name'] !== $conn)
                $connections[$conn][$var] = $input->getArgument($info['name']) ?? $info['default'];
        }

        $this->filesystem->dumpFile($this->inputFile, Yaml::dump($connections));

        return 0;
    }
}
