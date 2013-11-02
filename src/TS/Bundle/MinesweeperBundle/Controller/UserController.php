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
        $userManager = $this->get('ts_minesweeper.user_manager');

        return new JsonResponse($userManager->getAllUsersInfo());
    }

    /**
     * @Route("/{userId}")
     * @Method("GET")
     */
    public function userAction($userId)
    {
        $userManager = $this->get('ts_minesweeper.user_manager');

        return new JsonResponse($userManager->getUserInfo($userId));
    }

    /**
     * @Route("/{userId}/games", name="ts_minesweeper_user_games", options={"expose"=true})
     * @Method("GET")
     */
    public function userGamesAction($userId)
    {
        $userManager = $this->get('ts_minesweeper.user_manager');

        return new JsonResponse($userManager->getUserGames($userId));
    }
}
