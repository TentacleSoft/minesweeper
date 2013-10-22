<?php

namespace TS\Bundle\MinesweeperBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use TS\Bundle\MinesweeperBundle\Entity\User;

class GameManager
{
    const BOARD_SIZE = 16;
    const MINES = 49;

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
            return sprintf('%s <%s>', $player->getName(), $player->getUsername());
        }, $players));
        $chat = sprintf('<p class="info">Players: %s</p>', $playerNames);
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

    /**
     * @param int $gameId
     *
     * @return Game
     */
    public function get($gameId)
    {
        return $this->gameRepository->findOneById($gameId);
    }

    /**
     * Open cell
     *
     * @param Game $game
     * @param User $player
     * @param int $row
     * @param int $col
     *
     * @throws \Exception
     *
     * @return Game
     */
    public function open(Game $game, User $player, $row, $col)
    {
        $activePlayer = $game->getActivePlayer();
        if ($player->getId() !== $activePlayer) {
            throw new \Exception(sprintf('User %s is not currently active or game is already over', $activePlayer));
        }

        $players = $game->getPlayers();
        foreach ($players as $pos => $player) {
            if ($player->getId() === $activePlayer) {
                $this->openCell($game, $pos, $row, $col);

                $this->entityManager->flush();

                return $game;
            }
        }
    }

    /**
     * @param Game $game
     * @param User $user
     * @param string $text
     * @param string|null $type [info|error]
     *
     * @return Game
     */
    public function sendChat(Game $game, User $user, $text, $type = null)
    {
        if (null === $type) {
            $chat = sprintf('%s<p><span class="username">%s</span>%s</p>', $game->getChat(), $user->getUsername(), $text);
        } else {
            $chat = sprintf('%s<p class="%s">%s</p>', $game->getChat(), $type, $text);
        }

        $game->setChat($chat);

        $this->entityManager->flush();

        return $game;
    }

    /**
     * @param Game $game
     * @param int $playerPos
     * @param int $row
     * @param int $col
     *
     * @return string|null Symbol opened (if any)
     */
    private function openCell(Game &$game, $playerPos, $row, $col)
    {
        $board = $game->getBoard();
        $visibleBoard = $game->getVisibleBoard();

        if (!isset($board[$row][$col]) || $visibleBoard[$row][$col] !== Symbols::UNKNOWN) {
            return null;
        }

        $visibleBoard[$row][$col] = $board[$row][$col];

        if ($board[$row][$col] === Symbols::MINE) {
            $visibleBoard[$row][$col] .= $playerPos;

            $scores = $game->getScores();
            $scores[$playerPos] += 1;

            // End game (no next turn, a player has already won)
            if ($scores[$playerPos] > static::MINES / 2) {
                $game->setActivePlayer(Symbols::GAME_OVER);
            }

            $game->setScores($scores);
        } else {
            $players = $game->getPlayers();
            $nextPlayerPos = ($playerPos + 1) % count($players);

            $game->setActivePlayer($players[$nextPlayerPos]->getId());
        }

        $game->setVisibleBoard($visibleBoard);

        if (0 === $board[$row][$col]) {
            $this->openCell($game, $playerPos, $row - 1, $col - 1);
            $this->openCell($game, $playerPos, $row - 1, $col    );
            $this->openCell($game, $playerPos, $row - 1, $col + 1);
            $this->openCell($game, $playerPos, $row    , $col - 1);
            $this->openCell($game, $playerPos, $row    , $col + 1);
            $this->openCell($game, $playerPos, $row + 1, $col - 1);
            $this->openCell($game, $playerPos, $row + 1, $col    );
            $this->openCell($game, $playerPos, $row + 1, $col + 1);
        }

        return $board[$row][$col];
    }
}
