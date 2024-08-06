<?php

namespace App\DataFixtures;

use App\Entity\JobPosition;
use App\Repository\CompanyRepository;
use App\Repository\FormOfEmploymentRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobPositionFixture extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(private readonly CompanyRepository          $companyRepository,
                                private readonly UserRepository             $userRepository,
                                private readonly FormOfEmploymentRepository $formOfEmploymentRepository)
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $companies = $this->companyRepository->findAll();
        $users = $this->userRepository->findAll();
        $formOfEmployments = $this->formOfEmploymentRepository->findAll();

        foreach ($users as $user) {
            $alreadyActive = false;
            for ($i = 0; $i < random_int(0, 3); $i++) {
                $jobPosition = new JobPosition();
                $jobPosition->setName($this->faker->jobTitle());
                $jobPosition->setIndividual($user);
                $jobPosition->setCompany($companies[array_rand($companies)]);
                $jobPosition->setStartDate($this->faker->dateTimeBetween('-5 years', '-2 years'));
                $jobPosition->setEndDate(mt_rand() / mt_getrandmax() < 0.9 ? $this->faker->dateTimeBetween('-2 years', 'now') : null);
                $jobPosition->setDescription($this->faker->paragraph(random_int(0, 5)));
                $jobPosition->setFormOfEmployment($formOfEmployments[array_rand($formOfEmployments)]);

                if (!$alreadyActive && mt_rand() / mt_getrandmax() < 0.5) {
                    $jobPosition->setActive(true);
                    $alreadyActive = true;
                } else {
                    $jobPosition->setActive(false);
                }

                $manager->persist($jobPosition);
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }
}