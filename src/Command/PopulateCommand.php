<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Formatter;
use SychO\Populater\Database\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ProgressBar\Manager as ProgressBar;
use Codedungeon\PHPCliColors\Color;

class PopulateCommand extends Command
{
    /**
     * @var \SychO\Populater\Formatter
     */
    protected $formatter;

    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->formatter = new Formatter();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Command info
        $this
            ->setDescription('Populates a table')
            ->setHelp('This command populates a specified database table with fake data');

        // Arguments
        $this
            ->addArgument('blueprint', InputArgument::REQUIRED, 'Blueprint to use')
            ->addArgument('rows', InputArgument::REQUIRED, 'Number of rows');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $table = $input->getArgument('blueprint');
        $rows = (int) $input->getArgument('rows');

        $this->formatter->setBlueprint($table);
        $progress = new ProgressBar(0, $rows, 80, "\u{25AE}", ' ', ' ');
        $progress->setFormat(Color::RED . '[%bar%]' . Color::LIGHT_GREEN . ' %percent%% %eta%');

        for ($i=1; $i<=$rows; $i++) {
            Database::table($table)->insert($this->formatter->format());
            $progress->advance();
        }

        $output->writeln("...\n...\n...");
        $output->writeln(Color::RED . 'Finished in ' . Color::LIGHT_GREEN . round(microtime(true) - $start, 3) . Color::RED . 'ms');

        return 0;
    }
}
