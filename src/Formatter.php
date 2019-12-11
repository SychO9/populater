<?php

namespace SychO\Populater;

use Symfony\Component\Yaml\Yaml;
use Faker\Factory;

class Formatter
{
    /**
     * @var array $blueprint
     */
    public $blueprint;

    /**
     * @var \Faker\Factory
     */
    public $faker;

    /**
     * @param string $blueprint
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @param string $blueprint
     * @return \SychO\Populater\Formatter
     */
    public function setBlueprint(string $blueprint)
    {
        $this->blueprint = Yaml::parseFile(__DIR__ . '/../blueprints/' . env('DB_NAME') . '/' . $blueprint . '.yml');

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

            if (method_exists($this, $method['name']))
                $data[$column] = $this->{$method['name']}(...$method['params']);
            else
                $data[$column] = $this->faker->{$method['name']}(...$method['params']);
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
     * @return string
     */
    public function dateTime(...$params): string
    {
        return $this->faker->dateTime(...$params)->format('Y-m-d\TH:i:s.u');
    }

    /**
     *
     */
    public function autoIncrement()
    {
        return 0;
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
