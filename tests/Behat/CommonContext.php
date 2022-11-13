<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Doctrine;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class CommonContext implements Context
{

    /** @var KernelInterface */
    private $kernel;


    private $doctrine;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->doctrine = $kernel->getContainer()->get('doctrine');
    }

    /**
     * @Given the database is empty
     */
    public function theDatabaseIsEmpty()
    {
        $this->loadFixtures([]);
    }

    /**
     * @param $fixtures
     */
    private function loadFixtures($fixtures)
    {
        $loader = new Loader();
        $purger = new ORMPurger();

        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);

        (new ORMExecutor($this->doctrine->getManager(), $purger))
            ->execute($loader->getFixtures());
    }
}
