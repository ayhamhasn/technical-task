<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine;
use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpKernel\KernelInterface;


/**
 * Defines application features from the specific context.
 */
class ApiFeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */


    /**
     * The request POST payload
     * @var string
     */
    protected $requestPostPayload;

    /**
     * The respobse http code
     * @var int
     */
    protected $responseCode;

    /**
     * @var GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var array
     */
    private $data = [];

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->doctrine = $kernel->getContainer()->get('doctrine');
    }


    /**
     * @return Client
     */
    private function getGuzzleClient()
    {
        $guzzle_config = [];
        $guzzle_config['base_uri'] = 'http://host.docker.internal';

        $client = new Client($guzzle_config);

        return $client;
    }


    /**
     * @When I request :arg1
     */
    public function iRequest($arg1)
    {
        $requestParams = explode(' ', $arg1);
        $method = $requestParams[0];
        $uri = $requestParams[1];

        $options = [
            'verify' => false,
            'headers' => [
                'User-Agent' => 'testing/1.0',
                'Accept' => 'application/json',
            ],
            'body' => $this->requestPostPayload,
        ];

        try {
            $response = $this->getGuzzleClient()->request($method, $uri, $options);

            $this->responseCode = $response->getStatusCode();
            $this->response = $response;
        } catch (RequestException $e) {
            $this->responseCode = $e->getCode();
            $this->response = $e->getResponse();
        }

    }

    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        Assert::assertEquals((int)$arg1, $this->responseCode);
    }


    /**
     * @Then I see the json response:
     */
    public function iSeeTheJsonResponse(PyStringNode $string)
    {
        $expectedResponse = (string)$string;
        $expectedData = json_decode($expectedResponse);

        $response = $this->response->getBody()->getContents();
        $responseData = json_decode($response);

        Assert::assertEquals($expectedData, $responseData);
    }

    /**
     * @Then I see a correct json response:
     */
    public function iSeeACorrectJsonResponse(PyStringNode $string)
    {
        $response_string = $this->response->getBody()->getContents();
        $response = json_decode($response_string, true);
        $response_data = $response['data'];
        $correctJsonData = json_decode((string)$string, true);

        Assert::assertTrue(key_exists('data', $correctJsonData));

        $correctData = $correctJsonData['data'];

        Assert::assertEquals(array_keys($correctData), array_keys($response_data));
    }

    /**
     * @Then I see a correct json failure response:
     */
    public function iSeeACorrectJsonFailureResponse(PyStringNode $string)
    {
        $response_string = $this->response->getBody()->getContents();
        $response = json_decode($response_string, true);

        $correctJsonData = json_decode((string)$string, true);
        Assert::assertEquals(array_keys($correctJsonData), array_keys($response));
    }

    /**
     * @Then the response header :header is :headerValue
     */
    public function theResponseHeaderIs($header, $headerValue)
    {
        $responseHeader = $this->response->getHeader($header)[0];

        Assert::assertEquals($headerValue, $responseHeader);
    }


    /**
     * @Then the response :target should count :expectedNumber elements
     */
    public function theResponseShouldCountElements($target, $expectedNumber)
    {
        $responseData = $this->rewindAndRetriveResponseData();
        $targetData = $this->multiDimensionalArrayDataRetriever($target, $responseData);
        $actualNumber = count($targetData);

        Assert::assertEquals($expectedNumber, $actualNumber);
    }

    /**
     * @Then the response :target is :arg2
     */
    public function theResponseIs($target, $expectedValue)
    {
        $responseData = $this->rewindAndRetriveResponseData();
        $targetData = $this->multiDimensionalArrayDataRetriever($target, $responseData);

        if (true === $targetData) {
            $targetData = 'true';
        } elseif (false === $targetData) {
            $targetData = 'false';
        } elseif (null === $targetData) {
            $targetData = 'null';
        }

        Assert::assertEquals($expectedValue, $targetData);
    }


    /**
     * @Then the response :target is not :arg2
     */
    public function theResponseIsNot($target, $expectedValue)
    {
        $responseData = $this->rewindAndRetriveResponseData();

        $targetData = $this->multiDimensionalArrayDataRetriever($target, $responseData);

        if (true === $targetData) {
            $targetData = 'true';
        } elseif (false === $targetData) {
            $targetData = 'false';
        }

        Assert::assertNotEquals($expectedValue, $targetData);
    }

    /**
     * @Then the response :target contains :expectedValue
     */
    public function theResponseContains($target, $expectedValue)
    {
        $responseData = $this->rewindAndRetriveResponseData();
        $targetData = $this->multiDimensionalArrayDataRetriever($target, $responseData);

        $expectedValueArray = explode(', ', $expectedValue);

        Assert::assertEquals($expectedValueArray, $targetData);
    }

    /**
     * @Then each response :loopTarget's :target should count :expectedNumber elements
     */
    public function theEachResponsSShouldCountElements($loopTarget, $target, $expectedNumber)
    {
        $responseData = $this->rewindAndRetriveResponseData();

        $loopTargetData = $this->multiDimensionalArrayDataRetriever($loopTarget, $responseData);

        foreach ($loopTargetData as $element) {
            $actualNumber = count($this->multiDimensionalArrayDataRetriever($target, $element));
            Assert::assertEquals($expectedNumber, $actualNumber);
        }
    }

    /**
     * @Then each response :loopTarget's :target is :expectedValue
     */
    public function eachResponseSIs($loopTarget, $target, $expectedValue)
    {
        $responseData = $this->rewindAndRetriveResponseData();

        $loopTargetData = $this->multiDimensionalArrayDataRetriever($loopTarget, $responseData);

        foreach ($loopTargetData as $element) {
            $actualValue = $this->multiDimensionalArrayDataRetriever($target, $element);

            if (true === $actualValue) {
                $actualValue = 'true';
            } elseif (false === $actualValue) {
                $actualValue = 'false';
            }

            Assert::assertEquals($expectedValue, $actualValue);
        }
    }

    /**
     * @Then each response :loopTarget's :target contains :expectedValue
     */
    public function eachResponseSContains($loopTarget, $target, $expectedValue)
    {
        $responseData = $this->rewindAndRetriveResponseData();

        $loopTargetData = $this->multiDimensionalArrayDataRetriever($loopTarget, $responseData);

        foreach ($loopTargetData as $element) {
            $expectedValueArray = explode(', ', $expectedValue);
            $actualValue = $this->multiDimensionalArrayDataRetriever($target, $element);
            Assert::assertEquals($expectedValueArray, $actualValue);
        }
    }


    private function rewindAndRetriveResponseData()
    {
        $this->response->getBody()->rewind();
        $responseString = $this->response->getBody()->getContents();
        $responseData = json_decode($responseString, true);

        return $responseData;
    }

    private function multiDimensionalArrayDataRetriever($target, array $dataArray)
    {
        $dataArrayCoord = explode('->', $target);
        $currentLevel = $dataArray;

        foreach ($dataArrayCoord as $key) {
            $currentLevel = $currentLevel[$key];
        }

        return $currentLevel;
    }

    /**
     * @Then the response :target should be null
     */
    public function theNullResponseIs($target)
    {
        if (is_null($target)) {
            $result = true;
        } else {
            $result = false;
        }

        Assert::assertEquals(true, $result);
    }

}
