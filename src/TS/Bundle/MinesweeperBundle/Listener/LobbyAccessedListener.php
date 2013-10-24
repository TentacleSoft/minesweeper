<?php

namespace TS\Bundle\MinesweeperBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use TS\Bundle\MinesweeperBundle\Entity\Lobby;

class LobbyAccessedListener
{
    const CHAT_IDLE_TIME = 60;

    public function onLobbyLoad(LifecycleEventArgs $event)
    {
        /** @var Lobby $entity */
        $entity = $event->getEntity();
        if (!$entity instanceof Lobby) {
            return;
        }

        //aquests canvis seran persistits sempre?
        foreach ($entity->getOnlineUsers() as $user) {
            if (time() - $user->getTimeLastMessage()->getTimestamp() > static::CHAT_IDLE_TIME) {
                $entity->removeOnlineUser($user);
                $user->setLobby(null);
            }
        }
    }
}
