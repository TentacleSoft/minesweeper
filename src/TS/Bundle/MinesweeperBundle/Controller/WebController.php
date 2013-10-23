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
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $globals = array(
            'user' => array(
                'id' => $user->getId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
            )
        );

        return array('globals' => $globals);
    }
}
