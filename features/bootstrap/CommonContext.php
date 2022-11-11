<?php

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpKernel\KernelInterface;

class CommonContext implements Context
{

    private $kernel;
    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine['doctrine'];
    }

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * @BeforeSuite
     */
    public static function bootstrapApp()
    {

    }

    /**
     * @AfterSuite
     */
    public static function teardownApp()
    {

    }


    /**
     * @Given the database is empty
     */
    public function theDatabaseIsEmpty()
    {

    }


    /**
    * @Given I am not logged in
    */
    public function iAmNotLoggedIn() {

    }


    private function disableForeingKeyChecks()
    {
        /** @var Doctrine\DBAL\Connection $conn */
        $conn = $this->getDoctrineDefaultConnection();

        $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
    }

    private function enableForeingKeyChecks()
    {
        /** @var Doctrine\DBAL\Connection $conn */
        $conn = $this->getDoctrineDefaultConnection();

        $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function getDoctrineDefaultConnection()
    {
        return $this->getContainer()->get('doctrine.dbal.default_connection');
    }
}
