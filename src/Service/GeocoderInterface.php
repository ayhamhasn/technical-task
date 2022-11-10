<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;

interface GeocoderInterface
{
    public function supports(string $type): bool;

    public function geocode(Address $address, ResolvedAddressRepository $resolvedAddressRepository): ?Coordinates;
}
