<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use TS\Bundle\MinesweeperBundle\Service\Symbols;

/**
 * @Route("/games")
 */
class GameController extends Controller
{
    /**
     * @Route("/")
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
        $playerIdsArray = explode(',', $playerIds);
        foreach ($playerIdsArray as $playerId) {
            $players[] = $userRepository->findOneById($playerId);
        }

        try {
            $game = $gameManager->create($players, $activePlayer);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Failed to create game: ' . $e->getMessage());
        }

        return new JsonResponse($this->getGameInfo($game));
    }

    /**
     * @Route("/{gameId}")
     * @Method("GET")
     */
    public function gameAction($gameId)
    {
        return new JsonResponse($this->getGameInfo($this->getGame($gameId)));
    }

    /**
     * @Route("/{gameId}")
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

        /** @var Game */
        $game = $this->getGame($gameId);

        $gameManager = $this->get('ts_minesweeper.game_manager');
        $gameManager->open($game, $this->getUser(), $row, $col);

        return new JsonResponse($this->getGameInfo($game));
    }

    /**
     * @Route("/{gameId}/chat")
     * @Method("POST")
     */
    public function sendChatAction($gameId)
    {
        $request = $this->getRequest();

        $text = $request->get('text');

        if (empty($text)) {
            throw new BadRequestHttpException('Empty text');
        }

        $gameManager = $this->get('ts_minesweeper.game_manager');
        $gameManager->sendUserChat($gameId, $this->getUser(), $text);

        return new Response($gameManager->get($gameId));
    }

    /**
     * @param Game $game
     *
     * @return array
     */
    private function getGameInfo(Game $game)
    {
        $players = array();
        foreach ($game->getPlayers() as $player) {
            $players[] = array(
                'id' => $player->getId(),
                'name' => $player->getName(),
                'username' => $player->getUsername()
            );
        }

        return array(
            'players'      => $players,
            'activePlayer' => $game->getActivePlayer(),
            'scores'       => $game->getScores(),
            'board'        => $game->getVisibleBoard(),
            'chat'         => $game->getChat()
        );
    }
}
