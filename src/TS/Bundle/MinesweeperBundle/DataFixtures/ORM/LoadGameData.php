<?php

namespace TS\Bundle\MinesweeperBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use TS\Bundle\MinesweeperBundle\Service\BoardFactory;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $game = new Game();

        $board = BoardFactory::create(16, 50);

        $players = array(
            $this->getReference('user1')->getId(),
            $this->getReference('user2')->getId()
        );

        $game->setBoard($board);
        $game->setVisibleBoard($board);
        $game->setChat('Example chat');
        $game->setPlayers($players);

        $manager->persist($game);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
