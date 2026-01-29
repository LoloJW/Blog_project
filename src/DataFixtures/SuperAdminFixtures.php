<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperAdminFixtures extends Fixture
{
    // private ?UserPasswordHasherInterface $userPasswordHasher = null;

    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $superAdmin = $this->createSuperAdmin();

        $manager->persist($superAdmin);
        $manager->flush();
    }

    private function createSuperAdmin(): User
    {
        $superAdmin = new User();

        $superAdmin->setFirstName('Jean');
        $superAdmin->setLastName('Dupont');
        $superAdmin->setEmail('cuisine-du-monde@gmail.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER']);
        $superAdmin->setIsVerified(true);

        $userPasswordHasher = $this->userPasswordHasher->hashPassword($superAdmin, 'azerty1234A*');
        $superAdmin->setPassword($userPasswordHasher);

        $superAdmin->setCreatedAt(new \DateTimeImmutable());
        $superAdmin->setUpdatedAt(new \DateTimeImmutable());
        $superAdmin->setVerifiedAt(new \DateTimeImmutable());

        return $superAdmin;
    }
}
