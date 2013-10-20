<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

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
     * @Route("/{id}")
     * @Method("GET")
     */
    public function gameAction($id)
    {
        $game = $this->getDoctrine()->getRepository('TSMinesweeperBundle:Game')->find($id);

        if (!$game) {
            throw new NotFoundHttpException(sprintf('Game %s not found', $id));
        }

        return new JsonResponse($this->getGameInfo($game));
    }

    /**
     * @Route("/{id}")
     * @Method("POST")
     */
    public function openCellAction($id)
    {
        $request = $this->getRequest();

        $row = $request->get('row');
        $col = $request->get('col');

        if (is_null($row) || is_null($col)) {
            throw new BadRequestHttpException('Row or col empty');
        }

        $game = $this->getDoctrine()->getRepository('TSMinesweeperBundle:Game')->find($id);

        if (!$game) {
            throw new NotFoundHttpException(sprintf('Game %s not found', $id));
        }

        $this->openCell($game, $row, $col);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return new JsonResponse($this->getGameInfo($game));
    }

    /**
     * @param Game $game
     *
     * @return array
     */
    private function getGameInfo(Game $game)
    {
        return array(
            'players' => $game->getPlayers(),
            'board' => $game->getVisibleBoard(),
            'chat' => $game->getChat()
        );
    }

    /**
     * @param Game $game
     * @param int $row
     * @param int $col
     */
    private function openCell(Game &$game, $row, $col)
    {
        $board = $game->getBoard();
        $visibleBoard = $game->getVisibleBoard();

        if (!isset($board[$row][$col]) || $visibleBoard[$row][$col] !== Symbols::UNKNOWN) {
            return;
        }

        $visibleBoard[$row][$col] = $board[$row][$col];
        $game->setVisibleBoard($visibleBoard);

        if (0 === $board[$row][$col]) {
            $this->openCell($game, $row - 1, $col - 1);
            $this->openCell($game, $row - 1, $col    );
            $this->openCell($game, $row - 1, $col + 1);
            $this->openCell($game, $row    , $col - 1);
            $this->openCell($game, $row    , $col + 1);
            $this->openCell($game, $row + 1, $col - 1);
            $this->openCell($game, $row + 1, $col    );
            $this->openCell($game, $row + 1, $col + 1);
        }
    }
}
