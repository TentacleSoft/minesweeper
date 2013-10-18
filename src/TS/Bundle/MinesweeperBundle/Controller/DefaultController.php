<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $randId = mt_rand();

        $globals = array(
            'user' => array(
                'name' => 'User ' . $randId,
                'username' => 'provuser' . $randId,
            )
        );

        return array('globals' => $globals);
    }

    /**
     * @Route("/match/{matchId}/chat")
     * @Method("GET")
     * @Template()
     */
    public function chatAction()
    {
        $chat = 'This is the chat and now it\'s ' . time() . '!';

        return array('chat' => $chat);
    }
}
