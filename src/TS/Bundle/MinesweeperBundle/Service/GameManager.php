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
            throw new \Exception(sprintf('User %s is not currently active', $activePlayer));
        }

        $openedCell = $this->openCell($game, $row, $col);

        if (Symbols::MINE !== $openedCell) {
            $players = $game->getPlayers();
            $nextPlayerPos = 0;
            foreach ($players as $pos => $player) {
                if ($player->getId() === $activePlayer) {
                    $nextPlayerPos = ($pos + 1) % count($players);
                    break;
                }
            }
            $game->setActivePlayer($players[$nextPlayerPos]->getId());
        }

        $this->entityManager->flush();

        return $game;
    }

    /**
     * @param Game $game
     * @param User $user
     * @param string $text
     *
     * @return Game
     */
    public function sendChat(Game $game, User $user, $text)
    {
        $game->setChat(sprintf('%s<p>%s</p>', $game->getChat(), $text));

        $this->entityManager->flush();

        return $game;
    }

    /**
     * @param Game $game
     * @param int $row
     * @param int $col
     *
     * @return string|null Symbol opened (if any)
     */
    private function openCell(Game &$game, $row, $col)
    {
        $board = $game->getBoard();
        $visibleBoard = $game->getVisibleBoard();

        if (!isset($board[$row][$col]) || $visibleBoard[$row][$col] !== Symbols::UNKNOWN) {
            return null;
        }

        $visibleBoard[$row][$col] = $board[$row][$col];
        $game->setVisibleBoard($visibleBoard);

        if (Symbols::MINE === $board[$row][$col]) {
            $game->setScores($game->getScores()[0] + 1);
        } elseif (0 === $board[$row][$col]) {
            $this->openCell($game, $row - 1, $col - 1);
            $this->openCell($game, $row - 1, $col    );
            $this->openCell($game, $row - 1, $col + 1);
            $this->openCell($game, $row    , $col - 1);
            $this->openCell($game, $row    , $col + 1);
            $this->openCell($game, $row + 1, $col - 1);
            $this->openCell($game, $row + 1, $col    );
            $this->openCell($game, $row + 1, $col + 1);
        }

        return $board[$row][$col];
    }
}
