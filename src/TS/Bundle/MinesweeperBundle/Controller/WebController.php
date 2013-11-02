<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class WebController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     * @Template("::base.html.twig")
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $userData = array(
            'id' => $user->getId(),
            'name' => $user->getName(),
            'username' => $user->getUsername()
        );

        return array('user' => $userData);
    }
}
