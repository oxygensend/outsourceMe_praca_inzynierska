<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Repository\JobOfferRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Provider\Base;

class ApplicationFixture extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(private readonly UserRepository     $userRepository,
                                private readonly JobOfferRepository $jobOfferRepository)
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $users = $this->userRepository->findAllDevelopers();
        dump(count($users)); // 1000 (1000 users are developers
        $jobOffers = $this->jobOfferRepository->findAll();
        $appSatuts = [-1, 0, 1];

        foreach ($jobOffers as $jobOffer) {
            $user = $users[array_rand($users)];

            $application = new Application();
            $application->setJobOffer($jobOffer);
            $application->setIndividual($user);
            $application->setStatus($appSatuts[array_rand($appSatuts)]);
            $application->setDescription($this->faker->paragraph(random_int(0, 2)));
            $application->setDeleted(mt_rand() / mt_getrandmax() < 0.3);

            $manager->persist($application);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}