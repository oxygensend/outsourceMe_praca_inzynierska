<?php

namespace App\DataFixtures;

use App\Entity\JobOffer;
use App\Entity\SalaryRange;
use App\Repository\AddressRepository;
use App\Repository\FormOfEmploymentRepository;
use App\Repository\TechnologyRepository;
use App\Repository\UserRepository;
use App\Repository\WorkTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobOfferFixture extends BaseFixture implements OrderedFixtureInterface
{
    private const SIZE = 10000;

    public function __construct(private readonly UserRepository             $userRepository,
                                private readonly FormOfEmploymentRepository $formOfEmploymentRepository,
                                private readonly AddressRepository          $addressRepository,
                                private readonly WorkTypeRepository         $workTypeRepository,
                                private readonly TechnologyRepository       $technologyRepository
    )


    {
    }

    protected function loadData(ObjectManager $manager)
    {
        $users = $this->userRepository->findAllPrincipals();
        dump(count($users)); // 1000 (1000 users are developers
        $formOfEmployments = $this->formOfEmploymentRepository->findAll();
        $addresses = $this->addressRepository->findAll();
        $workTypes = $this->workTypeRepository->findAll();
        $technologies = $this->technologyRepository->findAll();

        for ($i = 0; $i < self::SIZE; $i++) {
            $jobOffer = new JobOffer();
            $jobOffer->setName($this->faker->jobTitle());
            $jobOffer->setDescription($this->faker->paragraph(random_int(0, 15)));
            $jobOffer->setFormOfEmployment($formOfEmployments[array_rand($formOfEmployments)]);
            $jobOffer->setAddress(mt_rand() / mt_getrandmax() < 0.8 ? $addresses[array_rand($addresses)] : null);
            $jobOffer->setExperience(JobOffer::EXPERIENCE_CHOICES[array_rand(JobOffer::EXPERIENCE_CHOICES)]);
            $jobOffer->setUser($users[array_rand($users)]);
            $jobOffer->setArchived(mt_rand() / mt_getrandmax() < 0.3);
            $jobOffer->setValidTo(mt_rand() / mt_getrandmax() < 0.75 ? $this->faker->dateTimeBetween('now', '+1 year') : null);
            $jobOffer->setTechnologies($this->getTechnologies($technologies));
            $jobOffer->setSalaryRange($this->getSalaryRange());
            $jobOffer->addWorkType($workTypes[array_rand($workTypes)]);
            $jobOffer->setNumberOfApplications(mt_rand(0, 100));
            $jobOffer->setRedirectCount($this->faker->numberBetween(0, 100));
            $manager->persist($jobOffer);

            if ($i % 100 === 0) {
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

    private function getSalaryRange()
    {
        $downRange = mt_rand() / mt_getrandmax() * 10000;
        $upRange = $downRange + mt_rand() / mt_getrandmax() * 10000;

        $salaryRange = new SalaryRange();
        $salaryRange->setDownRange($downRange);
        $salaryRange->setUpRange($upRange);
        $salaryRange->setCurrency(SalaryRange::CURRENCIES_CHOICES[mt_rand(0, count(SalaryRange::CURRENCIES_CHOICES) - 1)]);
        $salaryRange->setType(SalaryRange::TYPE_CHOICES[mt_rand(0, count(SalaryRange::TYPE_CHOICES) - 1)]);

        return mt_rand() / mt_getrandmax() < 0.7 ? $salaryRange : null;
    }

    public function getOrder()
    {
        return 9;
    }

}