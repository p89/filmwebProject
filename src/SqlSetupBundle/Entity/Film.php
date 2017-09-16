<?php

namespace SqlSetupBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
* Film
*
* @ORM\Table(name="film", indexes={@ORM\Index(name="search_idx", columns={"rating", "year", "votes", "seqnum"})})
* @ORM\Entity(repositoryClass="SqlSetupBundle\Repository\FilmRepository")
*/

class Film
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
     * @ORM\Column(name="FilmwebID", type="integer", unique=true)
     */
    private $filmwebID;

    /**
     * @var string
     *
     * @ORM\Column(name="plTitle", type="string", length=255, nullable=true)
     */
    private $plTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="enTitle", type="string", length=255, nullable=true)
     */
    private $enTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="Rating", type="float", nullable=true)
     */
    private $rating;

    /**
     * @var int
     *
     * @ORM\Column(name="Votes", type="integer", nullable=true)
     */
    private $votes;

    /**
     * @var int
     *
     * @ORM\Column(name="Duration", type="smallint", nullable=true)
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Premiere", type="date", nullable=true)
     */
    private $premiere;

    /**
     * @var int
     *
     * @ORM\Column(name="Year", type="smallint", nullable=true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="Review", type="text", nullable=true)
     */
    private $review;

    /**
     * @var int
     *
     * @ORM\Column(name="seqnum", type="integer", nullable=true)
     */
    private $seqnum;


    /**
     * @var string
     *
     * @ORM\Column(name="imagepath", type="text", nullable=true)
     */
    private $imagepath;


    /**
     * @ORM\ManyToMany(targetEntity="Country", inversedBy="film")
     * @ORM\JoinTable(name="film_country")
     */
    protected $country;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="film")
     * @ORM\JoinTable(name="film_genre")
     */
    protected $genre;


    /**
     * @ORM\ManyToMany(targetEntity="Director", inversedBy="film")
     * @ORM\JoinTable(name="film_director")
     */
    protected $director;

    /**
     * @ORM\ManyToMany(targetEntity="Screenwriter", inversedBy="film")
     * @ORM\JoinTable(name="film_screenwriter")
     */
    protected $screenwriter;

    public function __construct() {
        $this->director = new ArrayCollection();
        $this->screenwriter = new ArrayCollection();
        $this->genre = new ArrayCollection();
        $this->country = new ArrayCollection();
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
     * Set filmwebID
     *
     * @param integer $filmwebID
     *
     * @return Film
     */
    public function setFilmwebID($filmwebID)
    {
        $this->filmwebID = $filmwebID;

        return $this;
    }

    /**
     * Get filmwebID
     *
     * @return int
     */
    public function getFilmwebID()
    {
        return $this->filmwebID;
    }

    /**
     * Set rating
     *
     * @param string $rating
     *
     * @return Film
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     *
     * @return Film
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Film
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set premiere
     *
     * @param \DateTime $premiere
     *
     * @return Film
     */
    public function setPremiere($premiere)
    {
        $this->premiere = $premiere;

        return $this;
    }

    /**
     * Get premiere
     *
     * @return \DateTime
     */
    public function getPremiere()
    {
        return $this->premiere;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Film
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Film
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set review
     *
     * @param string $review
     *
     * @return Film
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set imagepath
     *
     * @param string $imagepath
     *
     * @return Film
     */
    public function setImagepath($imagepath)
    {
        $this->imagepath = $imagepath;

        return $this;
    }

    /**
     * Get imagepath
     *
     * @return string
     */
    public function getImagepath()
    {
        return $this->imagepath;
    }



    /**
     * Set country
     *
     * @param integer $country
     *
     * @return Film
     */
    public function addCountry($country)
    {
        $this->country[] = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return int
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set genre
     *
     * @param integer $genre
     *
     * @return Film
     */
    public function addGenre($genre)
    {
        $this->genre[] = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return int
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set director
     *
     * @param integer $director
     *
     * @return Film
     */
    public function addDirector($director)
    {
        $this->director[] = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return int
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set screenwriter
     *
     * @param integer $screenwriter
     *
     * @return Film
     */
    public function addScreenwriter($screenwriter)
    {
        $this->screenwriter[] = $screenwriter;

        return $this;
    }

    /**
     * Get screenwriter
     *
     * @return int
     */
    public function getScreenwriter()
    {
        return $this->screenwriter;
    }

    /**
     * Set plTitle
     *
     * @param string $plTitle
     *
     * @return Film
     */
    public function setPlTitle($plTitle)
    {
        $this->plTitle = $plTitle;

        return $this;
    }

    /**
     * Get plTitle
     *
     * @return string
     */
    public function getPlTitle()
    {
        return $this->plTitle;
    }

    /**
     * Set enTitle
     *
     * @param string $enTitle
     *
     * @return Film
     */
    public function setEnTitle($enTitle)
    {
        $this->enTitle = $enTitle;

        return $this;
    }

    /**
     * Get enTitle
     *
     * @return string
     */
    public function getEnTitle()
    {
        return $this->enTitle;
    }
}
