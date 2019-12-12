<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\Formatter\Formatter;
use SychO\Populater\Database\Database;
use SychO\Populater\Exception\FileReadException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

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

        try {
            $this->formatter->setBlueprint($table);
        } catch (FileReadException $e) {
            $output->writeln("<fg=yellow>{$e->getMessage()}</>");
            return 0;
        }

        // Check if the table exists
        if (!Database::schema('default')->hasTable($table))
        {
            $output->writeln("<fg=yellow>Table `<fg=red>$table</>` does not exist, make sure you create the table before populating it.</>");
            return 0;
        }

        $progress = new ProgressBar($output, $rows);
        $progress->setBarCharacter("\u{25AE}");
        $progress->setEmptyBarCharacter('.');
        $progress->setProgressCharacter('');
        $progress->setFormat('<fg=red>[%bar% ]</> <fg=green>%percent:3s%% %estimated:-6s% %memory:6s%</>');
        $progress->setBarWidth(50);
        $progress->maxSecondsBetweenRedraws(0.2);
        $progress->minSecondsBetweenRedraws(0.1);
        $progress->start();

        for ($i=1; $i<=$rows; $i++) {
            try {
                Database::table($table)->insert($this->formatter->format());
            } catch (\Exception $e) {
                $output->writeln("<fg=yellow>{$e->getMessage()}</>");
                return 0;
            }

            $progress->advance();
        }

        $progress->finish();
        $output->writeln("\n...\n...\n...");
        $output->writeln('<fg=red>Finished in <fg=green>'.round(microtime(true) - $start, 3).'</>ms</>');

        return 0;
    }
}
