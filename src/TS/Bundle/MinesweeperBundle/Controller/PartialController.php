<?php

namespace TS\Bundle\MinesweeperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to render partial templates for the AngularJS frontend
 */
class PartialController extends Controller
{
    /**
     * This action simply looks for a partial template for the page name and
     * returns it if found.
     *
     * @Route("/partials/{pageName}", requirements={"pageName"="[a-zA-Z]+"}, name="ts_minesweeper_partials", options={"expose"=true})
     * @Method("GET")
     */
    public function partialAction($pageName)
    {
        $user = $this->getUser();

        $userData = array(
            'id' => $user->getId(),
            'name' => $user->getName(),
            'username' => $user->getUsername()
        );

        try {
            return $this->render(
                'TSMinesweeperBundle:Partial:' . $pageName . '.html.twig',
                array('user' => $userData)
            );
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }
}
