<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;

class DummyGeocoder implements GeocoderInterface
{
    public function geocode(Address $address, ResolvedAddressRepository $resolvedAddressRepository): ?Coordinates
    {
        return new Coordinates(55.90742079144914, 21.135541627577837);
    }

    public function supports(string $type): bool
    {
        return false;
    }
}
