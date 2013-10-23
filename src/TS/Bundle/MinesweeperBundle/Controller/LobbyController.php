<?php


namespace TS\Bundle\MinesweeperBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * Class LobbyController
 *
 * @package TS\Bundle\MinesweeperBundle\Controller
 * @Route("/lobby"
 */
class LobbyController extends BaseController
{
    /**
     * @method("POST")
     * @route("/chat")
     */
    function chatPostAction(Request $request)
    {
        if (!$request->request->has('message')) {
            throw new \HttpRequestException();
        }

        $doctrine = $this->getDoctrine();
        $lobbyArray = $doctrine->getRepository('TSMinesweeperBundle:Lobby')->findAll();

        /** @var \TS\Bundle\MinesweeperBundle\Entity\Lobby $lobby */
        $lobby = $lobbyArray[0];

        $userId = $this->getUser()->getId();

        $message = $request->request->get('message');

        $lobby->addChatLine($userId, $message);

        $doctrine->getManager()->flush();

        return new JsonResponse(
            array(
                'chat' => $lobby->getChat(),
            ),
            200
        );
    }

    /**
     * @method("GET")
     * @route("/chat")
     */
    function lobbyInfoAction()
    {

    }
}