<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Car;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         $car = new Car();
         $car->setRegistration(str_replace('-', '', "AA-229-AA"))
            ->setBrand('Hyundai')
            ->setModel('i20')
            ->setStatus('en attente');
         $manager->persist($car);

        $manager->flush();
    }
}
