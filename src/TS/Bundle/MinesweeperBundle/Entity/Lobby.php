<?php

namespace TS\Bundle\MinesweeperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Lobby
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Lobby
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array
     *
     * @ORM\Column(name="chat", type="json_array")
     */
    private $chat;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="players")
     */
    private $onlineUsers;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set chat
     *
     * @param array $chat
     * @return Lobby
     */
    public function setChat($chat)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat
     *
     * @return array
     */
    public function getChat()
    {
        return $this->chat;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->onlineUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add onlineUsers
     *
     * @param \TS\Bundle\MinesweeperBundle\Entity\User $onlineUsers
     * @return Lobby
     */
    public function addOnlineUser(\TS\Bundle\MinesweeperBundle\Entity\User $onlineUsers)
    {
        $this->onlineUsers[] = $onlineUsers;

        return $this;
    }

    /**
     * Remove onlineUsers
     *
     * @param \TS\Bundle\MinesweeperBundle\Entity\User $onlineUsers
     */
    public function removeOnlineUser(\TS\Bundle\MinesweeperBundle\Entity\User $onlineUsers)
    {
        $this->onlineUsers->removeElement($onlineUsers);
    }

    /**
     * Get onlineUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOnlineUsers()
    {
        return $this->onlineUsers;
    }

    /**
     * @param int $from
     * @param string $message
     */
    public function addChatLine($from, $message)
    {
        if (!is_array($this->chat)) {
            $this->chat = array();
        }

        $this->chat[] = array(
            'from' => $from,
            'message' => $message,
        );
    }
}
