<?php

namespace App\DataFixtures;

use App\Entity\Education;
use App\Repository\UniversityRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EducationFixture extends BaseFixture implements OrderedFixtureInterface
{

    public function __construct(private readonly UniversityRepository $universityRepository,
                                private readonly UserRepository       $userRepository)
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $unis = $this->universityRepository->findAll();
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < random_int(0, 3); $i++) {
                $education = new Education();
                $education->setIndividual($user);
                $education->setUniversity($unis[array_rand($unis)]);
                $education->setGrade($this->faker->randomFloat(2, 2, 5));
                $education->setFieldOfStudy($this->faker->word());
                $education->setStartDate($this->faker->dateTimeBetween('-10 years', '-5 years'));
                $education->setEndDate($this->faker->dateTimeBetween('-5 years', 'now'));
                $education->setDescription($this->faker->paragraph(random_int(0, 5)));
                $education->setTitle($this->faker->word());
                $manager->persist($education);
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}