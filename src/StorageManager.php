<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater;

use SychO\Populater\Exception\FileReadException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Finder\Finder;

class StorageManager
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private static $filesystem;

    /**
     *
     */
    public static function writeToEnv(array $vars)
    {
        $env = [];

        foreach ($vars as $var => $value)
            $env[] = "$var=$value";

        self::getFileSystem()->dumpFile(__DIR__.'/../.env', implode("\n", $env));
    }

    /**
     * @param string $filename
     * @param string|array $content an array will be parsed to yaml
     * @return void
     */
    public static function writeTo(string $filename, $content)
    {
        if (is_array($content))
            return self::writeArrayTo($filename, $content);

        self::getFileSystem()->dumpFile(self::storage($filename), (string) $content);
    }

    /**
     * @param string $filename
     * @param array $content array will be parsed to yaml
     * @return void
     */
    public static function writeArrayTo(string $filename, array $content)
    {
        self::writeTo($filename, Yaml::dump($content));
    }

    /**
     * Parses the file content to yaml and returns an array
     * @param string $filename
     * @return array
     */
    public static function read(string $filename): array
    {
        try {
            $data = Yaml::parseFile(self::storage($filename));
        } catch (ParseException $e) {
            throw new FileReadException($e->getMessage());
        }

        return $data;
    }

    /**
     * Lists all files of a directory in "storage/"
     */
    public static function getBlueprints(): array
    {
        $blueprints = [];
        $finder = new Finder();
        $finder->files()->in(self::blueprints());

        foreach ($finder as $file)
            $blueprints[$file->getFilename()] = $file;

        return $blueprints;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    public static function getFileSystem()
    {
        if (!isset(self::$filesystem))
            self::$filesystem = new Filesystem();

        return self::$filesystem;
    }

    /**
     * Returns the full path to a file $arguments[0] located in storage/$method
     */
    public static function __callStatic($method, $arguments = null): string
    {
        if ($method !== 'storage')
            $method = 'storage/'.$method;

        return __DIR__.'/../'.$method.'/'.($arguments[0] ?? '');
    }
}
