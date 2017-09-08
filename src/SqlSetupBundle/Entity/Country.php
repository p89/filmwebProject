<?php

namespace SqlSetupBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* Country
*
* @ORM\Table(name="country", indexes={@ORM\Index(name="search_idx", columns={"name"})})
* @ORM\Entity(repositoryClass="SqlSetupBundle\Repository\CountryRepository")
*/

class Country
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
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Film", mappedBy="country")
     */
    protected $film;

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
     * @return Country
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
     * @return Country
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
