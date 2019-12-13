<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Test;

use SychO\Populater\StorageManager;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    public static function saveConnections(): void
    {
        try {
            StorageManager::rename(
                StorageManager::path('connections.yml'),
                StorageManager::path('connections.tmp.yml')
            );
        } catch (\Exception $e) {
            // ...
        }
    }

    public static function restoreConnections(): void
    {
        StorageManager::remove(StorageManager::path('connections.yml'));

        try {
            StorageManager::rename(
                StorageManager::path('connections.tmp.yml'),
                StorageManager::path('connections.yml')
            );
        } catch (\Exception $e) {
            StorageManager::remove(StorageManager::path('connections.yml'));
        }
    }
}
