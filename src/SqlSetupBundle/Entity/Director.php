<?php

namespace SqlSetupBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* Director
*
* @ORM\Table(name="director", indexes={@ORM\Index(name="search_idx", columns={"name", "filmwebid"})})
* @ORM\Entity(repositoryClass="SqlSetupBundle\Repository\DirectorRepository")
*/

class Director
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
     * @var int
     *
     * @ORM\Column(name="Filmwebid", type="integer")
     */
    private $filmwebid;

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Film", mappedBy="director")
     */
    private $film;

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
     * Set filmwebid
     *
     * @param integer $filmwebid
     *
     * @return Director
     */
    public function setFilmwebID($filmwebid)
    {
        $this->filmwebid = $filmwebid;

        return $this;
    }

    /**
     * Get filmwebid
     *
     * @return int
     */
    public function getFilmwebID()
    {
        return $this->filmwebid;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Director
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
     * @return Director
     */
    public function setFilm($film)
    {
        $this->film = $film;

        return $this;
    }

    /**
     * Get film
     *
     * @return int
     */
    public function getFilm()
    {
        return $this->film;
    }
}
