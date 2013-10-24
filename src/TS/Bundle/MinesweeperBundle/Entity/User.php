<?php
// src/Acme/UserBundle/Entity/User.php

namespace TS\Bundle\MinesweeperBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Game", mappedBy="players")
     */
    private $games;

    /**
     * @var Lobby
     *
     * @ORM\ManyToOne(targetEntity="Lobby", inversedBy="onlineUsers")
     */
    private $lobby;

    /**
     * @var int
     *
     * @ORM\Column(type="datetime")
     */
    private $timeLastMessage;

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
        $this->timeLastMessage = new \DateTime();
    }
    
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
     * Add games
     *
     * @param \TS\Bundle\MinesweeperBundle\Entity\Game $games
     * @return User
     */
    public function addGame(\TS\Bundle\MinesweeperBundle\Entity\Game $games)
    {
        $this->games[] = $games;
    
        return $this;
    }

    /**
     * Remove games
     *
     * @param \TS\Bundle\MinesweeperBundle\Entity\Game $games
     */
    public function removeGame(\TS\Bundle\MinesweeperBundle\Entity\Game $games)
    {
        $this->games->removeElement($games);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Set lobby
     *
     * @param \TS\Bundle\MinesweeperBundle\Entity\Lobby $lobby
     * @return User
     */
    public function setLobby(\TS\Bundle\MinesweeperBundle\Entity\Lobby $lobby = null)
    {
        $this->lobby = $lobby;
    
        return $this;
    }

    /**
     * Get lobby
     *
     * @return \TS\Bundle\MinesweeperBundle\Entity\Lobby 
     */
    public function getLobby()
    {
        return $this->lobby;
    }

    /**
     * Set timeLastMessage
     *
     * @param \DateTime $timeLastMessage
     * @return User
     */
    public function setTimeLastMessage(\DateTime $timeLastMessage)
    {
        $this->timeLastMessage = $timeLastMessage;
    
        return $this;
    }

    /**
     * Get timeLastMessage
     *
     * @return \DateTime 
     */
    public function getTimeLastMessage()
    {
        return $this->timeLastMessage;
    }
}