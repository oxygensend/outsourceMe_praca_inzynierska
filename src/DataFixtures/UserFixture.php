<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\TechnologyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends BaseFixture implements OrderedFixtureInterface
{
    private const SIZE = 3000;

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher,
                                private readonly AddressRepository           $addressRepository,
                                private readonly TechnologyRepository        $technologyRepository
    )
    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $addresses = $this->addressRepository->findAll();
        $technologies = $this->technologyRepository->findAll();

        for ($i = 0; $i < self::SIZE; $i++) {

            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $user->setName($this->faker->firstName());
            $user->setSurname($this->faker->lastName());
            $user->setPassword($this->passwordHasher->hashPassword($user, "password"));
            $user->setPhoneNumber(substr($this->faker->phoneNumber(),0,9));
            $user->setDescription($this->faker->paragraph(random_int(0, 10)));
            $user->setDateOfBirth($this->faker->dateTimeBetween('-50 years', '-18 years'));
            $user->setRedirectCount($this->faker->numberBetween(0, 100));
            $user->setEmailConfirmedAt($this->faker->dateTimeBetween('-1 years', 'now'));
            $user->setAddress($addresses[array_rand($addresses)]);
            $user->setLinkedinUrl($this->faker->url());
            $user->setActiveJobPosition($this->faker->jobTitle());

            if ($i % 3 === 0) {
                $user->setRoles(['ROLE_PRINCIPAL']);
                $user->setAccountType("Principal");
            } else {
                $user->setRoles(['ROLE_DEVELOPER']);
                $user->setAccountType("Developer");
                $user->setLookingForJob(mt_rand() / mt_getrandmax() < 0.65);
                $user->setExperience(User::EXPERIENCE_CHOICES[array_rand(User::EXPERIENCE_CHOICES)]);
                $user->setTechnologies($this->getTechnologies($technologies));
                $user->setGithubUrl($this->faker->url());
            }

            $manager->persist($user);

            if($i % 100 === 0) {
                $manager->flush();
            }
        }

        $manager->flush();
    }

    private function getTechnologies(array $technologies): Collection
    {
        shuffle($technologies);
        $count = random_int(0, (int)(count($technologies)/2));
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $technologies[$i];
        }
        return new ArrayCollection($result);
    }

    public function getOrder(): int
    {
        return 1;
    }
}