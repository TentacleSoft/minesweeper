<?php

namespace TS\Bundle\MinesweeperBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use TS\Bundle\MinesweeperBundle\Service\BoardFactory;
use TS\Bundle\MinesweeperBundle\Service\Symbols;

class LoadGameData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $game = new Game();

        $board = BoardFactory::create(16, 50);
        $visibleBoard = array();
        foreach (range(0, 15) as $row) {
            foreach (range(0, 15) as $col) {
                $visibleBoard[$row][$col] = Symbols::UNKNOWN;
            }
        }

        $players = array(
            $this->getReference('user1')->getId(),
            $this->getReference('user2')->getId()
        );

        $game->setBoard($board);
        $game->setVisibleBoard($visibleBoard);
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
