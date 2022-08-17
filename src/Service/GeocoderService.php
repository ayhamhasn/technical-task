<?php

namespace App\Service;

use App\ValueObject\Address;
use App\ValueObject\Coordinates;

class GeocoderService
{
    /**
     * @var GeocoderInterface[]
     */
    private array $geocoders;

    public function __construct(iterable $geocoders)
    {
        $this->geocoders = iterator_to_array($geocoders);
    }

    public function handle (string $type, Address $address) : ?Coordinates
    {
        foreach($this->geocoders as $geocoder) {
            if ($geocoder->supports($type)) {
                return $geocoder->geocode($address);
            }
        }
        return null;
    }
}