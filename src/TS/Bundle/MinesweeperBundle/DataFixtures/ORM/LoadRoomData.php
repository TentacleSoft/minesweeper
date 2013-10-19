<?php

namespace TS\Bundle\MinesweeperBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use TS\Bundle\MinesweeperBundle\Entity\Room;

class LoadRoomData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $room = new Room();

        $users = array(
            $this->getReference('user1')->getId(),
            $this->getReference('user2')->getId()
        );

        $room->setName('Example Room');
        $room->setUsers($users);
        $room->setChat('You are on Example Room');

        $manager->persist($room);
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
