<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TS\Bundle\MinesweeperBundle\Entity\Game;
use TS\Bundle\MinesweeperBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function usersAction()
    {
        $users = $this->getDoctrine()->getRepository('TSMinesweeperBundle:User')->findAll();

        return new JsonResponse(
            array_map(function ($user) {
                return $this->getUserInfo($user);
            }, $users)
        );
    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     */
    public function userAction($id)
    {
        $user = $this->getUserInstance($id);

        return new JsonResponse($this->getUserInfo($user));
    }

    /**
     * @Route("/{id}/games")
     * @Method("GET")
     */
    public function getUserGames($id)
    {
        $user = $this->getUserInstance($id);

        $games = array_map(function ($game) {
            $players = array();

            foreach ($game->getPlayers() as $player) {
                $players[] = array(
                    'id' => $player->getId(),
                    'name' => $player->getName(),
                    'username' => $player->getUsername()
                );
            }

            return array(
                'id'           => $game->getId(),
                'players'      => $players,
                'activePlayer' => $game->getActivePlayer(),
                'scores'       => $game->getScores(),
                'board'        => $game->getVisibleBoard(),
                'chat'         => $game->getChat()
            );
        }, $user->getGames()->toArray());

        return new JsonResponse($games);
    }

    private function getUserInstance($id)
    {
        $userRepository = $this->getDoctrine()->getRepository('TSMinesweeperBundle:User');

        if (is_numeric($id)) {
            $user = $userRepository->find($id);
        } else {
            $user = $userRepository->findOneByUsername($id);
        }

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User %s not found', $id));
        }

        return $user;
    }

    private function getUserInfo(User $user)
    {
        $active = array();
        $won = array();
        $lost = array();

        foreach ($user->getGames() as $game) {
            if ($game->isOver()) {
                if ($game->getWinner()->getId() === $user->getId()) {
                    $won[] = $this->getGameInfo($game);
                } else {
                    $lost[] = $this->getGameInfo($game);
                }
            } else {
                $active[] = $this->getGameInfo($game);
            }
        }

        return array(
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'games' => array(
                'active' => $active,
                'won' => $won,
                'lost' => $lost,
            )
        );
    }

    private function getGameInfo(Game $game)
    {
        return array(
            'id' => $game->getId(),
            'scores' => $game->getScores(),
        );
    }
}
