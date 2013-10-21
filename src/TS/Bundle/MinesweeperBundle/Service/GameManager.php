<?php

namespace TS\Bundle\MinesweeperBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use TS\Bundle\MinesweeperBundle\Entity\User;

class GameManager
{
    const BOARD_SIZE = 16;
    const MINES = 50;

    /**
     * @var EntityRepository
     */
    private $gameRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityRepository $gameRepository, EntityManager $entityManager)
    {
        $this->gameRepository = $gameRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User[] $players
     * @param int|null $activePlayer
     *
     * @return Game
     */
    public function create(array $players, $activePlayer = null)
    {
        $game = new Game();

        $scores = array();
        foreach ($players as $player) {
            $game->addPlayer($player);
            $player->addGame($game);
            $this->entityManager->persist($player);
            $scores[] = 0;
        }

        $game->setScores($scores);

        $playerNames = implode(', ', array_map(function (User $player) {
            return $player->getName();
        }, $players));
        $chat = sprintf('Game %d (players: %s)', $game->getId(), $playerNames);
        $game->setChat($chat);

        if (null === $activePlayer) {
            $activePlayer = array_rand(array_map(function (User $player) {
                return $player->getId();
            }, $players));
        }
        $game->setActivePlayer($activePlayer);

        $game->setBoard(BoardFactory::create(static::BOARD_SIZE, static::MINES));

        $visibleBoard = array();
        foreach (range(0, 15) as $row) {
            foreach (range(0, 15) as $col) {
                $visibleBoard[$row][$col] = Symbols::UNKNOWN;
            }
        }
        $game->setVisibleBoard($visibleBoard);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }
}
