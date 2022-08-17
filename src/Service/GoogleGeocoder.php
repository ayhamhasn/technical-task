<?php

namespace App\Service;

use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use GuzzleHttp\Client;

class GoogleGeocoder implements GeocoderInterface
{
    public function supports(string $type): bool
    {
        return 'Google' === $type;
    }

    public function geocode(Address $address): ?Coordinates
    {
        $apiKey = $_ENV["GOOGLE_GEOCODING_API_KEY"];

        $params = [
            'query' => [
                'address' => $address->getStreet(),
                'components' => implode('|', ["country:{$address->getCountry()}", "locality:{$address->getCity()}", "postal_code:{$address->getPostcode()}"]),
                'key' => $apiKey
            ]
        ];

        $client = new Client();

        $response = $client->get('https://maps.googleapis.com/maps/api/geocode/json', $params);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);


        if (count($data['results']) === 0) {
            return null;
        }

        $firstResult = $data['results'][0];

        if ($firstResult['geometry']['location_type'] !== 'ROOFTOP') {
            return null;
        }

        return new Coordinates( $firstResult['geometry'] ['location'] ['lat'], $firstResult['geometry'] ['location'] ['lng']);
    }
}