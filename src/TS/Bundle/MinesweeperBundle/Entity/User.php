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
     * @var array
     *
     * @ORM\Column(name="matches", type="array")
     */
    private $matches;

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
     * Set matches
     *
     * @param array $matches
     * @return User
     */
    public function setMatches(array $matches)
    {
        $this->matches = $matches;

        return $this;
    }

    /**
     * Add match
     *
     * @param $match
     */
    public function addMatch($match)
    {
        $this->matches[] = $match;
    }

    /**
     * Get matches
     *
     * @return array
     */
    public function getMatches()
    {
        return $this->matches;
    }
}
