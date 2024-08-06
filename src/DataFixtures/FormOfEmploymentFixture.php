<?php

namespace App\DataFixtures;
use App\Entity\FormOfEmployment;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FormOfEmploymentFixture extends BaseFixture implements OrderedFixtureInterface
{

    protected function loadData(ObjectManager $manager)
    {
        $formOfEmployments = ["Pelen etat", "Niepelny etat", "Umowa zlecenia", "Staz", "Freelance", "Praktyka"];
        foreach ($formOfEmployments as $formOfEmploymentName) {
            $formOfEmployment = new FormOfEmployment();
            $formOfEmployment->setName($formOfEmploymentName);
            $manager->persist($formOfEmployment);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}