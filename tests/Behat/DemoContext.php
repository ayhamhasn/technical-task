<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class DemoContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var Response|null */
    private $response;

    private $doctrine;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->doctrine = $kernel->getContainer()->get('doctrine');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://host.docker.internal/coordinates?countryCode=DE&city=berlin&street=ritterlandweg+26&postcode=13409');

        echo $response->getStatusCode(); // 200
        echo $response->getHeaderLine('content-type'); // 'application/json; charset=utf8'
        echo $response->getBody(); // '{"id"
    }

    /**
     * @When a demo scenario sends a request to :path
     */
    public function aDemoScenarioSendsARequestTo(string $path): void
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived(): void
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Then the application's kernel should use :expected environment
     */
    public function kernelEnvironmentShouldBe(string $expected): void
    {
        echo $this->kernel->getEnvironment();
        if ($this->kernel->getEnvironment() !== $expected) {
            throw new \RuntimeException();
        }
    }
}
