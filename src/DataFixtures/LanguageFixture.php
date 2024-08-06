<?php

namespace App\DataFixtures;

use App\Entity\Language;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LanguageFixture extends BaseFixture implements OrderedFixtureInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            for ($i = 0; $i < random_int(0, 3); $i++) {
                $language = new Language();
                $language->setIndividual($user);
                $language->setName($this->faker->languageCode());
                $language->setDescription($this->faker->paragraph(random_int(0, 1)));
                $manager->persist($language);
            }
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}