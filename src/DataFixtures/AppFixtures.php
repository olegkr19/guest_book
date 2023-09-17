<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(
       UserPasswordHasherInterface $hasher
    ) {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $user->setRoles(["ROLE_ADMIN"]);
        $password = $this->hasher->hashPassword($user, '12345678');
        $user->setPassword($password);
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('test2@test.com');
        $user->setRoles(["ROLE_USER"]);
        $password = $this->hasher->hashPassword($user2, '12345678');
        $user2->setPassword($password);
        $manager->persist($user2);

        $dateTime = new \DateTimeImmutable();

        $message1 = new Message();
        $message1->setUsername('test');
        $message1->setEmail('test@test.com');
        $user->setRoles(["ROLE_USER"]);
        $message1->setHomepage('https://www.test.com/');
        $message1->setText('test text');
        $message1->setCreatedAt($dateTime->format('Y-m-d H:i:s'));
        $manager->persist($message1);

        $message2 = new Message();
        $message2->setUsername('test2');
        $message2->setEmail('test2@test.com');
        $user->setRoles(["ROLE_USER"]);
        $message2->setHomepage('https://www.test2.com/');
        $message2->setText('test2 text');
        $message2->setCreatedAt($dateTime->format('Y-m-d H:i:s'));
        $manager->persist($message2);

        $manager->flush();
    }
}
