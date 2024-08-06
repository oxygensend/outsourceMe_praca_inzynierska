<?php

namespace App\DataFixtures;

use App\Entity\Opinion;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OpinionFixture extends BaseFixture implements OrderedFixtureInterface
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        foreach ($this->userRepository->findAll() as $user) {
            $rate = 0;
            $opinionCount = 0;
            for ($i = 0; $i < random_int(0, 15); $i++) {
                $opinion = new Opinion();
                $opinion->setToWho($user);
                $opinion->setFromWho($this->getAuthor($user, $this->userRepository->findAll()));
                $opinion->setScale(random_int(0, 5));
                $opinion->setDescription($this->faker->paragraph(random_int(0, 3)));
                $manager->persist($opinion);
                $rate += $opinion->getScale();
                $opinionCount++;
            }
            $user->setOpinionsRate($opinionCount === 0 ? $opinionCount : $rate / $opinionCount);
            $manager->persist($user);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 13;
    }

    /**
     * @param User $recipient
     * @param User[] $users
     */
    public function getAuthor(User $recipient, array $users): ?User
    {
        if (count($users) <= 1) {
            return null;
        }

        do {
            $randomIndex = mt_rand(0, count($users) - 1);
            $author = $users[$randomIndex];
        } while ($author->getId() == $recipient->getId());

        return $author;
    }
}