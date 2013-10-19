<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TS\Bundle\MinesweeperBundle\Entity\Game;
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
     * @Route("/{id}")
     * @Method("GET")
     */
    public function gameAction($id)
    {
        $game = $this->getDoctrine()->getRepository('TSMinesweeperBundle:Game')->find($id);

        if (!$game) {
            throw new NotFoundHttpException(sprintf('User %s not found', $id));
        }

        return new JsonResponse($this->getGameInfo($game));
    }

    private function getGameInfo(Game $game)
    {
        return array(
            'players' => $game->getPlayers(),
            'board' => $game->getBoard(),
            'chat' => $game->getChat()
        );
    }
}
