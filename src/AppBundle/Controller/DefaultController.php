<?php

namespace AppBundle\Controller;

use Doctrine\DBAL\Driver\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{

    /**
     * @Route("/", name="Main")
     * @Template("default/index.html.twig")
     */

    public function MainScreenAction (Connection $connection) {

        $sql = "SELECT f1.seqnum
                FROM film f1
                JOIN film_genre fg on fg.film_id = f1.id
                JOIN genre g on fg.genre_id = g.id
                JOIN (SELECT CEIL(RAND() *
                            (SELECT MAX(seqnum)
                                   FROM film f3
                                   )) AS seqnum) AS f2
                WHERE     f1.seqnum >= f2.seqnum
                      AND f1.Rating >= 7
                      AND f1.Votes >= 1000
                ORDER BY f1.seqnum ASC
                LIMIT 1";

        $randomID = $connection->fetchAll($sql);

        $em = $this->getDoctrine()->getManager();
        $actualFilm = $em->getRepository('SqlSetupBundle:Film')->findOneBySeqnum($randomID[0]['seqnum']);

        $countryList = $actualFilm->getCountry();
        $actualCountries = [];

        for ($i = 0; $i < count($countryList); $i++) {
            $actualCountries[] = $actualFilm->getCountry()[$i]->getName() ? $actualFilm->getCountry()[$i]->getName() : null;
        }

        $genreList = $actualFilm->getGenre();
        $actualGenres = [];

        for ($i = 0; $i < count($genreList); $i++) {
            $actualGenres[] = $actualFilm->getGenre()[$i]->getName() ? $actualFilm->getGenre()[$i]->getName() : null;
        }

        $directorsList = $actualFilm->getDirector();
        $actualDirectors = [];

        for ($i = 0; $i < count($directorsList); $i++) {
            $actualDirectors[] = $actualFilm->getDirector()[$i]->getName() ? $actualFilm->getDirector()[$i]->getName() : null;
        }


        $screenwritersList = $actualFilm->getScreenwriter();
        $actualScreenwriters = [];
        for ($i = 0; $i < count($screenwritersList); $i++) {
            $actualScreenwriters[] = $actualFilm->getScreenwriter()[$i]->getName() ? $actualFilm->getScreenwriter()[$i]->getName() : null;
        }

        $picturePath = $actualFilm->getImagepath();
        $coverSrc = empty($picturePath) ? "covers/nopicture.gif" : $picturePath;

        return ['coverSrc' => $coverSrc,
            'filmPlTitle' => $actualFilm->getPlTitle(),
            'filmEnTitle' => $actualFilm->getEnTitle(),
            'filmRating' => round($actualFilm->getRating(), 1),
            'filmGenre' => $actualGenres,
            'filmCountry' => $actualCountries,
            'filmYear' => $actualFilm->getYear(),
            'filmDirector' => $actualDirectors,
            'filmScreenwriter' => $actualScreenwriters,
            'filmDesc' => $actualFilm->getDescription(),
            'filmReview' => $actualFilm->getReview()
            ];
    }
}

