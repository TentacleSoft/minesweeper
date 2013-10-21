<?php

namespace TS\Bundle\MinesweeperBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use TS\Bundle\MinesweeperBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    private $userData = array(
        array(
            'id' => 1,
            'name' => 'Generated User 1',
            'username' => 'genUser1',
            'password' => '1234'
        ),
        array(
            'id' => 2,
            'name' => 'Generated User 2',
            'username' => 'genUser2',
            'password' => '1234'
        )
    );

    public function load(ObjectManager $manager)
    {
        foreach ($this->userData as $userData) {
            $user = new User();

            $user->setUsername($userData['username']);
            $user->setName($userData['name']);
            $user->setPassword(sha1($userData['password']));

            $manager->persist($user);

            $this->addReference('user' . $userData['id'], $user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
