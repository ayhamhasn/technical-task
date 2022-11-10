<?php

namespace App\Service;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;

class CacheDBGeocoder implements GeocoderInterface
{

    public function supports(string $type): bool
    {
        return 'CacheDB' === $type;
    }

    public function geocode(Address $address, ResolvedAddressRepository $resolvedAddressRepository): ?Coordinates
    {
        $resolvedAddress = $resolvedAddressRepository->getByAddress($address);

        if (!$resolvedAddress) {
            return null;
        }

        return new Coordinates($resolvedAddress->getLat(), $resolvedAddress->getLng());
    }
}