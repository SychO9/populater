<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Formatter;

use SychO\Populater\StorageManager;
use SychO\Populater\Formatter\Generator;
use SychO\Populater\Exception\GeneratorException;

class Formatter
{
    /**
     * @var array $blueprint
     */
    public $blueprint;

    /**
     * \SychO\Populater\Formatter\Generator
     */
    public $generator;

    /**
     * ...
     */
    public function __construct()
    {
        $this->generator = new Generator();
    }

    /**
     * @param string $blueprint
     * @return \SychO\Populater\Formatter
     */
    public function setBlueprint(string $blueprint)
    {
        $blueprint = $blueprint.'.yml';

        if (StorageManager::getFileSystem()->exists(StorageManager::blueprints(env('CONNECTION').'/'.$blueprint)))
            $blueprint = env('CONNECTION').'/'.$blueprint;
        elseif (!StorageManager::getFileSystem()->exists(StorageManager::blueprints($blueprint)))
            throw new FileReadException("Blueprint $blueprint not found.");

        try {
            $this->blueprint = StorageManager::read('blueprints/'.$blueprint);
        } catch (FileReadException $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function format(): array
    {
        $data = [];

        foreach ($this->blueprint['format'] as $column => $format) {
            $method = $this->getMethod($format);

            $data[$column] = $this->generator->{$method['name']}(...$method['params']);
            $type = gettype($data[$column]);

            if (in_array($type, ['array', 'object']))
                throw new GeneratorException("Error: The generator '$format' retuns a type of '$type'.");
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->blueprint['format']);
    }

    /**
     * @return array|bool
     */
    public function getMethod(string $format)
    {
        if (preg_match_all("/:?(\'[^\'\n]*\'|[^:\'\n]*)/s", $format, $matches) === false || empty($matches[1][0]))
            return false;

        $faker_method = $matches[1][0];
        unset($matches[1][0]);
        $params = [];

        foreach ($matches[1] as $match)
            $params[] = str_replace("'", "", $match);

        return [
            'name' => $faker_method,
            'params' => $params
        ];
    }

    /**
     * @param string $blueprint
     * @return \SychO\Populater\Formatter
     */
    public static function fromBlueprint(string $blueprint): self
    {
        $instance = new Formatter();
        $instance->setBlueprint($blueprint);

        return $instance;
    }
}
