<?php

namespace SqlSetupBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* Genre
*
* @ORM\Table(name="genre", indexes={@ORM\Index(name="search_idx", columns={"name"})})
* @ORM\Entity(repositoryClass="SqlSetupBundle\Repository\GenreRepository")
*/

class Genre
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Film", mappedBy="genre")
     */
    private $film;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=100, unique=true)
     */
    private $name;


    public function __construct()
    {
        $this->film = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Genre
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
     * Set film
     *
     * @param integer $film
     *
     * @return Genre
     */
    public function setFilm($film)
    {
        $this->film = $film;

        return $this;
    }

    /**
     * Get film
     *
     * @return integer
     */
    public function getFilm()
    {
        return $this->film;
    }
}
