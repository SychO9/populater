<?php
/**
 * @package sycho/populater
 * @author Sami 'SychO' Mazouz
 * @version 1.0.0
 * @license MIT
 */

namespace SychO\Populater\Formatter;

use Faker\Factory;

class Generator
{
    /**
     * @var \Faker\Factory
     */
    public $faker;

    /**
     * ...
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @return string
     */
    public function dateTime(...$params): string
    {
        return $this->faker->dateTime(...$params)->format('Y-m-d\TH:i:s.u');
    }

    /**
     * ...
     */
    public function autoIncrement()
    {
        return 0;
    }

    public function __call($method, $arguments)
    {
        return $this->faker->{$method}(...$arguments);
    }
}
