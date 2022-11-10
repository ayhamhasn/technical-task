<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewsletterGeneratorTest extends KernelTestCase
{
    public function testSomething()
    {
        self::bootKernel();

        $container = self::$container;

        print_r($container);
    }
}
