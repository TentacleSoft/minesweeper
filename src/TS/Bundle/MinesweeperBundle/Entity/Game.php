<?php

namespace TS\Bundle\MinesweeperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Game
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
     * @ORM\Column(name="players", type="array")
     */
    private $players;

    /**
     * @var array
     *
     * @ORM\Column(name="board", type="array")
     */
    private $board;

    /**
     * @var array
     *
     * @ORM\Column(name="visibleBoard", type="array")
     */
    private $visibleBoard;

    /**
     * @var array
     *
     * @ORM\Column(name="scores", type="array")
     */
    private $scores;

    /**
     * @var string
     *
     * @ORM\Column(name="chat", type="text")
     */
    private $chat;

    /**
     * @var integer
     *
     * @ORM\Column(name="activePlayer", type="integer")
     */
    private $activePlayer;


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
     * Set players
     *
     * @param array $players
     * @return Game
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    
        return $this;
    }

    /**
     * Get players
     *
     * @return array 
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set board
     *
     * @param array $board
     * @return Game
     */
    public function setBoard($board)
    {
        $this->board = $board;
    
        return $this;
    }

    /**
     * Get board
     *
     * @return array 
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set visibleBoard
     *
     * @param array $visibleBoard
     * @return Game
     */
    public function setVisibleBoard($visibleBoard)
    {
        $this->visibleBoard = $visibleBoard;
    
        return $this;
    }

    /**
     * Get visibleBoard
     *
     * @return array 
     */
    public function getVisibleBoard()
    {
        return $this->visibleBoard;
    }

    /**
     * Set scores
     *
     * @param array $scores
     * @return Game
     */
    public function setScores($scores)
    {
        $this->scores = $scores;
    
        return $this;
    }

    /**
     * Get scores
     *
     * @return array 
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set chat
     *
     * @param string $chat
     * @return Game
     */
    public function setChat($chat)
    {
        $this->chat = $chat;
    
        return $this;
    }

    /**
     * Get chat
     *
     * @return string 
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * Set activePlayer
     *
     * @param integer $activePlayer
     * @return Game
     */
    public function setActivePlayer($activePlayer)
    {
        $this->activePlayer = $activePlayer;
    
        return $this;
    }

    /**
     * Get activePlayer
     *
     * @return integer 
     */
    public function getActivePlayer()
    {
        return $this->activePlayer;
    }
}
