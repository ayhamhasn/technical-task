<?php

namespace App\DataFixtures;

use App\Entity\ResolvedAddress;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ResolvedAddressFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $resolvedAddress = new ResolvedAddress();
        $resolvedAddress
            ->setCountryCode('lt')
            ->setCity('vilnius')
            ->setStreet('jasinskio 16')
            ->setPostcode('01112');

        $manager->flush();
    }
}
