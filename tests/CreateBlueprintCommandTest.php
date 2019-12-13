<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Test;

use SychO\Populater\StorageManager;
use SychO\Populater\Command\CreateBlueprintCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateBlueprintCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new CreateBlueprintCommand('create:blueprint'));
        $commandTester = new CommandTester($application->find('create:blueprint'));
        $commandTester->setInputs([
            'id', 'autoIncrement',
            'name', 'firstName'
        ]);
        $commandTester->execute([
            'table_name' => 'fake_table',
            'number_of_columns' => 2,
            'database' => 'test'
        ]);
        $blueprint = (array) StorageManager::read('blueprints/test/fake_table.yml');
        $this->assertNotEmpty($blueprint['format']);
        $this->assertNotEmpty($blueprint['format']['id']);
    }

    public static function tearDownAfterClass(): void
    {
        try {
            StorageManager::remove(StorageManager::path('blueprints/test'));
        } catch (\Exception $e) {
            // ...
        }
    }
}
