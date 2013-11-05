<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use TS\Bundle\MinesweeperBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/games")
 */
class GameController extends Controller
{
    /**
     * @Route("/", name="ts_minesweeper_new_game", options={"expose"=true})
     * @Method("POST")
     */
    public function newGameAction()
    {
        $playerIds = $this->getRequest()->get('players');
        $activePlayer = $this->getRequest()->get('activePlayer');

        if (is_null($playerIds)) {
            throw new BadRequestHttpException('Player ids missing');
        }

        $gameManager = $this->get('ts_minesweeper.game_manager');
        $userRepository = $this->getDoctrine()->getRepository('TSMinesweeperBundle:User');

        $players = array();

        if (!in_array($this->getUser()->getId(), $playerIds)) {
            throw new BadRequestHttpException('Player is not in the new game');
        }

        foreach ($playerIds as $playerId) {
            $players[] = $userRepository->findOneById($playerId);
        }

        try {
            $gameId = $gameManager->create($players, $activePlayer);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Failed to create game: ' . $e->getMessage());
        }

        return new JsonResponse($this->getGameInfo($gameId));
    }

    /**
     * @Route("/{gameId}")
     * @Method("GET")
     */
    public function gameAction($gameId)
    {
        return new JsonResponse($this->getGameInfo($gameId));
    }

    /**
     * @Route("/{gameId}", name="ts_minesweeper_open_cell", options={"expose"=true})
     * @Method("POST")
     */
    public function openCellAction($gameId)
    {
        $request = $this->getRequest();

        $row = $request->get('row');
        $col = $request->get('col');

        if (is_null($row) || is_null($col)) {
            throw new BadRequestHttpException('Row or col empty');
        }

        $gameManager = $this->get('ts_minesweeper.game_manager');
        $gameManager->open($gameId, $this->getUser(), $row, $col);

        return new JsonResponse($this->getGameInfo($gameId));
    }

    /**
     * @Route("/{gameId}/chat", name="ts_minesweeper_send_chat_game", options={"expose"=true})
     * @Method("POST")
     */
    public function sendChatAction($gameId)
    {
        $request = $this->getRequest();

        $message = $request->get('message');

        if (empty($message)) {
            throw new BadRequestHttpException('Empty text');
        }

        $gameManager = $this->get('ts_minesweeper.game_manager');
        $gameManager->sendUserChat($gameId, $this->getUser(), $message);

        /** @var Game $game */
        $game = $this->get('ts_minesweeper.game_manager')->get($gameId);

        return new JsonResponse(
            array('chat' => $game->getChat()),
            200
        );
    }

    /**
     * @param int $gameId
     *
     * @return array
     */
    private function getGameInfo($gameId)
    {
        /** @var Game $game */
        $game = $this->get('ts_minesweeper.game_manager')->get($gameId);

        $scores = $game->getScores();
        $players = array();

        /** @var User $player */
        foreach ($game->getPlayers() as $key => $player) {
            $players[] = array(
                'id' => $player->getId(),
                'name' => $player->getName(),
                'username' => $player->getUsername(),
                'score' => $scores[$key]
            );
        }

        return array(
            'id'           => $gameId,
            'players'      => $players,
            'activePlayer' => $game->getActivePlayer(),
            'board'        => $game->getVisibleBoard(),
            'chat'         => $game->getChat()
        );
    }
}
