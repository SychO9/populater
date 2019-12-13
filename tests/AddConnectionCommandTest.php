<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Test;

use SychO\Populater\StorageManager;
use SychO\Populater\Command\AddConnectionCommand;
use Symfony\Component\Console\Tester\CommandTester;

class AddConnectionCommandTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::saveConnections();
    }

    public function testExecute()
    {
        $commandTester = new CommandTester(new AddConnectionCommand('add:connection'));
        $commandTester->execute([
            'connection_name' => 'test_connection',
            'driver' => 'mysql',
            'database' => 'test',
            'username' => 'test_user',
            'password' => 'test_pass',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Connection added', $output);

        $connections = StorageManager::read('connections.yml');
        $this->assertArrayHasKey('test_connection', $connections);
    }

    public static function tearDownAfterClass(): void
    {
        parent::restoreConnections();
    }
}
