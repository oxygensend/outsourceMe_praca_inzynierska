<?php

namespace App\DataFixtures;

use App\Entity\WorkType;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WorkTypeFixture extends BaseFixture implements OrderedFixtureInterface
{

    protected function loadData(ObjectManager $manager)
    {
        $workTypes = ["Remote", "Onsite", "Hybrid", "Office", "Negotiable"];
        foreach ($workTypes as $workTypeName) {
            $workType = new WorkType();
            $workType->setName($workTypeName);
            $manager->persist($workType);
        }

        $manager->flush();
    }

    public function getOrder()
    {

        return 8;
    }
}