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
use ProgressBar\Manager as ProgressBar;
use Codedungeon\PHPCliColors\Color;

class CreateBlueprintCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Command info
        $this
            ->setDescription('Creates a blueprint')
            ->setHelp('This command creates a new blueprint of the data to populate');

        // Arguments
        $this
            ->addArgument('database', InputArgument::REQUIRED, 'The database used')
            ->addArgument('table_name', InputArgument::REQUIRED, 'The name of the blueprint, has to be the same as the table\'s');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {


        return 0;
    }
}
