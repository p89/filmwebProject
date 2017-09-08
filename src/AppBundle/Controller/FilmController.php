<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;

class FilmController extends FOSRestController
{
    public function postFilmAction (Request $request) {

        $content = $request->request;

        $minRating = $content->get('minRating');
        $maxRating = $content->get('maxRating');
        $maxYear = $content->get('maxYear');
        $minYear = $content->get('minYear');
        $minVotes = $content->get('minVotes');
        $maxVotes = $content->get('maxVotes');
        $filmGenre = $content->get('filmGenre');

        /*$sql = "SELECT f1.id
               FROM film AS f1
               JOIN film_genre fg on fg.film_id = f1.id
               JOIN genre g on fg.genre_id = g.id
               WHERE
               g.Name = COALESCE(NULLIF(:filmGenre, ''), g.Name)
               AND Rating >= :minRating
               AND Rating <= :maxRating
               AND Year >= :minYear
               AND Year <= :maxYear
               ORDER BY rand() ASC
               LIMIT 1";*/

        $sql = "SELECT f1.seqnum
                FROM film f1
                JOIN film_genre fg on fg.film_id = f1.id
                JOIN genre g on fg.genre_id = g.id
                JOIN (SELECT CEIL(RAND() *
                            (SELECT MAX(seqnum)
                             FROM (
                                  SELECT f3.seqnum
                                  FROM film f3
                                  JOIN film_genre fg2 on fg2.film_id = f3.id
                                  JOIN genre g2 on fg2.genre_id = g2.id
                                  WHERE g2.Name = COALESCE(NULLIF(:filmGenre, ''), g2.Name)
                                        AND f3.Rating >= :minRating
                                        AND f3.Rating <= :maxRating
                                        AND f3.Year >= :minYear
                                        AND f3.Year <= :maxYear
                                        AND f3.Votes >= :minVotes
                                        AND f3.Votes <= :maxVotes
                                  ORDER BY seqnum DESC
                                  LIMIT 1
                                  ) maxfilmseq)) AS seqnum) AS f2
                WHERE f1.seqnum >= f2.seqnum
                        AND g.Name = COALESCE(NULLIF(:filmGenre, ''), g.Name)
                        AND f1.Rating >= :minRating
                        AND f1.Rating <= :maxRating
                        AND f1.Year >= :minYear
                        AND f1.Year <= :maxYear
                        AND f1.Votes >= :minVotes
                        AND f1.Votes <= :maxVotes
                ORDER BY f1.seqnum ASC
                LIMIT 1";

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':filmGenre', $filmGenre);
        $stmt->bindValue(':minRating', $minRating);
        $stmt->bindValue(':maxRating', $maxRating);
        $stmt->bindValue(':maxYear', $maxYear);
        $stmt->bindValue(':minYear', $minYear);
        $stmt->bindValue(':minVotes', $minVotes);
        $stmt->bindValue(':maxVotes', $maxVotes);
        $stmt->execute();
        $randomID = $stmt->fetchAll();

        if (count($randomID) == 0) {

            $alert = ["error" => "Brak filmów spełniających podane kryteria."];
            $view = $this->view(json_encode($alert),200);
            return $this->handleView($view);
        }

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

        $resultJSON = json_encode([
            'coverSrc' => $coverSrc,
            'filmPlTitle' => $actualFilm->getPlTitle(),
            'filmEnTitle' => $actualFilm->getEnTitle(),
            'filmRating' => round($actualFilm->getRating(), 1),
            'filmGenre' => $actualGenres,
            'filmCountry' => $actualCountries,
            'filmYear' => $actualFilm->getYear(),
            'filmDirector' => $actualDirectors,
            'filmScreenwriter' => $actualScreenwriters,
            'filmDesc' => $actualFilm->getDescription(),
            'filmReview' => $actualFilm->getReview(),
        ]);

        $view = $this->view($resultJSON,200);
        return $this->handleView($view);
    }
}