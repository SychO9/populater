<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Command;

use SychO\Populater\StorageManager;
use SychO\Populater\Formatter\Formatter;
use SychO\Populater\Formatter\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Faker\Factory;
use ReflectionClass;
use ReflectionMethod;

class CreateBlueprintCommand extends Command
{
    /**
     * @var array
     */
    private $faker_formatters;

    /**
     * @var \SychO\Populater\Formatter
     */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    public function __construct(?string $name)
    {
        parent::__construct($name);

        $this->faker_formatters = $this->getFakerFormatters();
        $this->formatter = new Formatter();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        // Command info
        $this
            ->setDescription('Creates a blueprint')
            ->setHelp($this->getHelp());

        // Arguments
        $this
            ->addArgument('table_name', InputArgument::REQUIRED, 'The name of the blueprint, has to be the same as the table\'s')
            ->addArgument('number_of_columns', InputArgument::REQUIRED, 'The number of columns')
            ->addArgument('database', InputArgument::OPTIONAL, 'The database used');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $heleper = $this->getHelper('question');
        $blueprint = [
            'format' => []
        ];
        $column = [];
        $filename = '';

        for ($i=1; $i<=$input->getArgument('number_of_columns'); $i++) {
            $output->writeln("<info>Column n°$i:</info> ");

            foreach (['name', 'generator'] as $item) {
                $question = new Question("\t<fg=red>$item:</> ", "numberBetween:1:10000");

                do {
                    $column[$item] = $heleper->ask($input, $output, $question);
                } while($item === 'generator' && !isset($this->faker_formatters[$this->formatter->getMethod($column[$item])['name']]));
            }

            $blueprint['format'][$column['name']] = $column['generator'];
        }

        if ($input->hasArgument('database'))
            $filename = $input->getArgument('database').'/';

        $filename .= $input->getArgument('table_name').'.yml';

        StorageManager::writeArrayTo('blueprints/'.$filename, $blueprint);

        return 0;
    }

    /**
     * @return array
     */
    public function getFakerFormatters(): array
    {
        $generator = Factory::create();
        $formatters = [];
        $providers = array_merge($generator->getProviders(), [
            Generator::class
        ]);

        foreach ($providers as $provider) {
            $reflection = new ReflectionClass($provider);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $object) {
                if (!in_array($object->name, ['__construct', '__call', '__callStatic', '__get', '__set', '__destruct']))
                    $formatters[$object->name] = $object;
            }
        }

        return $formatters;
    }

    /**
     *
     */
    public function getHelp()
    {
        $help_array = [
            'content' => [
                'This command creates a new blueprint of the data to populate',
                'A column generator format is as follows: <fg=green>method[:param1[:param2[:...]]]</>'
            ],
            'examples' => [
                ['name' => 'created_at', 'generator' => "dateTime:'2019-02-25 08:37:17':UTC"],
                ['name' => 'id_author', 'generator' => "numberBetween:1:10000"]
            ],
        ];

        $help = implode("\n", $help_array['content']);

        foreach ($help_array['examples'] as $example)
            $help .= "\nExample:\n\t<fg=green>name:</> {$example['name']}\n\t<fg=green>generator:</> {$example['generator']}";

        return $help;
    }
}
