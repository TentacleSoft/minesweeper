<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $globals = array(
            'user' => array(
                'name' => 'Provisional User',
                'username' => 'provuser',
            )
        );

        return $this->render(
            'TSMinesweeperBundle:Default:index.html.twig',
            array(
                'globals' => $globals
            )
        );
    }
}
