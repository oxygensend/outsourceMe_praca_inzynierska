<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyFixture extends BaseFixture implements OrderedFixtureInterface
{
    private const SIZE = 100;

    protected function loadData(ObjectManager $manager)
    {
        $companyNames = [];

        while (count($companyNames) < self::SIZE) {
            $name = $this->faker->unique()->company();
            if (!in_array($name, $companyNames)) {
                $companyNames[] = $name;
                $company = new Company();
                $company->setName($name);
                $manager->persist($company);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }

}