<?php

namespace TS\Bundle\MinesweeperBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use TS\Bundle\MinesweeperBundle\Exception\GameManagerNotFoundException;

class GameManagerNotFoundExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof GameManagerNotFoundException) {
            $response = new Response($exception->getMessage(), 404);

            $event->setResponse($response);
        }
    }
}
