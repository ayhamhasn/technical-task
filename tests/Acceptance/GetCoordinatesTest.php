<?php

namespace App\Tests\Acceptance;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetCoordinatesTest extends WebTestCase
{
    public function testGetCoordinates(): void
    {
        $client = static::createClient();
        $client->request('GET', '/coordinates', [
            'countryCode' => 'lt',
            'city' => 'vilnius',
            'street' => 'jasinskio 16',
            'postcode' => '01112',
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(['lat' => 54.6878265, 'lng' => 25.2609295]), $client->getResponse()->getContent());
    }

    public function testGetCoordinatesAnotherAddress(): void
    {
        $client = static::createClient();
        $client->request('GET', '/coordinates', [
            'countryCode' => 'de',
            'city' => 'berlin',
            'street' => 'ritterlandweg 20',
            'postcode' => '13409',
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(['lat' => 52.560218, 'lng' => 13.3718507]), $client->getResponse()->getContent());
    }

    public function testGetCoordinatesForGoogle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gmaps', [
            'country' => 'germany',
            'city' => 'berlin',
            'street' => 'ritterlandweg 20',
            'postcode' => '13409',
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(['lat' => 52.560218, 'lng' => 13.3718507]), $client->getResponse()->getContent());
    }


    public function testGetCoordinatesForHere(): void
    {
        $client = static::createClient();
        $client->request('GET', '/hmaps', [
            'country' => 'germany',
            'city' => 'berlin',
            'street' => 'ritterlandweg 20',
            'postcode' => '13409',
        ]);

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(['lat' => 52.56053, 'lng' => 13.37188]), $client->getResponse()->getContent());
    }
}