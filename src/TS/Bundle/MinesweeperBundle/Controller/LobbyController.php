<?php


namespace TS\Bundle\MinesweeperBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use TS\Bundle\MinesweeperBundle\Entity\User;

/**
 * Class LobbyController
 *
 * @package TS\Bundle\MinesweeperBundle\Controller
 * @Route("/lobby")
 */
class LobbyController extends BaseController
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function lobbyInfoAction()
    {
        $lobby = $this->getLobby();

        return new JsonResponse(
            array(
                'chat' => $lobby->getChat(),
                'users' => array_map(
                    function(User $user) {
                        return array(
                            'id' => $user->getId(),
                            'name' => $user->getUsername(),
                        );
                    },
                    $lobby->getOnlineUsers()->toArray()
                ),
            ),
            200
        );
    }

    /**
     * @Route("/chat")
     * @Method("POST")
     */
    function chatPostAction(Request $request)
    {
        if (!$request->request->has('message')) {
            throw new \HttpRequestException();
        }

        $lobby = $this->getLobby();

        $userId = $this->getUser()->getId();

        $message = $request->request->get('message');

        $lobby->addChatLine($userId, $message);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(
            array(
                'chat' => $lobby->getChat(),
            ),
            200
        );
    }

    /**
     * @return \TS\Bundle\MinesweeperBundle\Entity\Lobby
     */
    private function getLobby()
    {
        $lobbies = $this->getDoctrine()->getRepository('TSMinesweeperBundle:Lobby')->findAll();

        return $lobbies[0];
    }
}
