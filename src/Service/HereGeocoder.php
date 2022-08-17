<?php

namespace App\Service;

use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use GuzzleHttp\Client;

class HereGeocoder implements GeocoderInterface
{

    public function supports(string $type): bool
    {
        return 'Here' === $type;
    }

    public function geocode(Address $address): ?Coordinates
    {
        $apiKey = $_ENV["HEREMAPS_GEOCODING_API_KEY"];

        $params = [
            'query' => [
                'qq' => implode(';', ["country={$address->getCountry()}", "city={$address->getCity()}", "street={$address->getStreet()}", "postalCode={$address->getPostcode()}"]),
                'apiKey' => $apiKey
            ]
        ];

        $client = new Client();

        $response = $client->get('https://geocode.search.hereapi.com/v1/geocode', $params);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);


        if (count($data['items']) === 0) {
            return null;
        }

        $firstItem = $data['items'][0];

        if ($firstItem['resultType'] !== 'houseNumber') {
            return null;
        }

        return new Coordinates( $firstItem['position'] ['lat'], $firstItem['position'] ['lng']);
    }
}