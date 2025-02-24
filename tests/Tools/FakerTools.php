<?php

namespace App\Tests\Tools;

use Faker\Factory;
use Faker\Generator;

trait FakerTools
{
    public function getFaker(): Generator
    {
        return Factory::create();
    }
}
