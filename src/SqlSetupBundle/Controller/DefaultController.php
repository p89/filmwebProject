<?php

namespace SqlSetupBundle\Controller;

use Doctrine\DBAL\Driver\PDOException;
use SqlSetupBundle\Entity\Country;
use SqlSetupBundle\Entity\Director;
use SqlSetupBundle\Entity\Film;
use SqlSetupBundle\Entity\Genre;
use SqlSetupBundle\Entity\Screenwriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AccessPoints;


class DefaultController extends Controller
{
    /**
     * @Route("parser/{id}/{endId}")
     */

    public function loginFB ($id, $endId) {
        set_time_limit(0);

        $filmweb = AccessPoints\Filmweb::instance();

        $filmwebUser = $this->getParameter('filmweb_login');
        $filmwebPass = $this->getParameter('filmweb_pass');

        $filmweb->Login($filmwebUser, $filmwebPass)->execute();

        for ($i = $id; $i < $endId; $i++) {
                $this->insertAction($i, $filmweb);
        }
        return new Response('wartosc i: ' . $i);
    }

    /**
     * @Route("run/{startingID}/")
     */

    public function insertAction($startingID, $filmweb2)
    {
        // Invoke doctrine's Entity Manager

        $em = $this->getDoctrine()->getManager();

        $filmID = $startingID;
        $filmweb = $filmweb2;

        // Get objects from API


        if ($filmweb->getFilmInfoFull($filmID)->execute()) {
            try {
                $getFilmInfo = $filmweb->getFilmInfoFull($filmID)->execute();
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        } else {
            return false;
        }



        if ($filmweb->getFilmPersons($filmID, 2,0)->execute()) {
            try {
                $getScreenwriter = $filmweb->getFilmPersons($filmID, 2,0)->execute();
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        } else {
            return false;
        }


        if ($filmweb->getFilmPersons($filmID, 1, 0)->execute()) {
            try {
                $getDirector = $filmweb->getFilmPersons($filmID, 1, 0)->execute();
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        } else {
            return false;
        }

        if ($filmweb->getFilmReview($filmID)->execute()) {
            try {
                $getFilmReview = $filmweb->getFilmReview($filmID)->execute();
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        } else {
            return false;
        }

        // If the result is null (the ID doesn't exist) move to the next sequence number

        if(!$getFilmInfo) {

            //invoke operation with ID+1
            echo "skipping film with id " . $filmID . " (empty id) <br>";

        } else {

            //invoke operation with current ID

            $filmRepo = $em->getRepository('SqlSetupBundle:Film');


                // FILM - setting inputs for SQL table
                $film = new Film();

                $film->setEnTitle($getFilmInfo->originalTitle);
                if ($getFilmInfo->hasDescription != null) {
                    $film->setDescription($getFilmInfo->description);
                }
                $film->setRating($getFilmInfo->avgRate);
                if ($getFilmInfo->votesCount == "") {
                    $film->setVotes(0);
                } else {
                    $film->setVotes($getFilmInfo->votesCount);
                }
                $film->setYear($getFilmInfo->year);
                $film->setDuration($getFilmInfo->duration);
                $film->setPremiere(new \DateTime($getFilmInfo->premiereWorld));
                $film->setPlTitle($getFilmInfo->title);
                $film->setImagepath($getFilmInfo->imagePath);
                $film->setFilmwebID($filmID);

                if (is_object($getFilmReview)) {
                    if ($getFilmReview->authorName != null) {
                        $film->setReview($getFilmReview->review);
                    }
                }


                // DIRECTOR - setting inputs for SQL table

                if ($getDirector && !empty($getDirector)) {

                    $dirRepo = $em->getRepository('SqlSetupBundle:Director');
                    $directorsAdded = [];

                    foreach ($getDirector as $k => $v) {

                        $directorAlreadyAdded = $dirRepo->findOneByFilmwebid($v->personId);


                        if ($directorAlreadyAdded) {
                            if (!in_array($v->personId, $directorsAdded)) {
                                $film->addDirector($directorAlreadyAdded);
                                $directorsAdded[] = $v->personId;
                            }
                        }
                        else {
                            if (!in_array($v->personId, $directorsAdded)) {
                                $director = new Director();
                                $director->setFilmwebID($v->personId);
                                $director->setName($v->personName);
                                $film->addDirector($director);
                                $directorsAdded[] = $v->personId;

                                try {
                                    $em->persist($director);
                                } catch (PDOException $e) {
                                    echo "Exception: " . $e->getMessage() . '<br>';
                                }
                            }
                        }
                    }
                }

                // SCREENWRITER - setting inputs for SQL table

                if ($getScreenwriter && !empty($getScreenwriter)) {

                    $scrRepo = $em->getRepository('SqlSetupBundle:Screenwriter');
                    $screenwriterAdded = [];

                    foreach ($getScreenwriter as $k => $v) {

                        $screenAlreadyAdded = $scrRepo->findOneByFilmwebid($v->personId);

                        if ($screenAlreadyAdded) {
                            if (!in_array($v->personId, $screenwriterAdded)) {
                                $film->addScreenwriter($screenAlreadyAdded);
                                $screenwriterAdded[] = $v->personId;
                            }
                        }
                        else {
                            if (!in_array($v->personId, $screenwriterAdded)) {
                                $screenwriter = new Screenwriter();
                                $screenwriter->setFilmwebID($v->personId);
                                $screenwriter->setName($v->personName);
                                $film->addScreenwriter($screenwriter);
                                $screenwriterAdded[] = $v->personId;

                                try {
                                    $em->persist($screenwriter);
                                } catch (PDOException $e) {
                                    echo "Exception: " . $e->getMessage() . '<br>';
                                }
                            }


                        }
                    }
                }

                // GENRE - setting inputs for SQL table

                if ($getFilmInfo->genres && !empty($getFilmInfo->genres)) {
                    $filmGenres = explode(",", $getFilmInfo->genres);
                    foreach ($filmGenres as $k => $v) {

                        $genRepo = $em->getRepository('SqlSetupBundle:Genre');
                        $genreAlreadyAdded = $genRepo->findOneByName($v);

                        if ($genreAlreadyAdded) {
                            $film->addGenre($genreAlreadyAdded);
                        }
                        else {
                            $genre = new Genre();
                            $genre->setName($v);
                            $film->addGenre($genre);
                            $em->persist($genre);
                        }
                    }
                }

                // COUNTRY - setting inputs for SQL table

                if ($getFilmInfo->countriesString && !empty($getFilmInfo->countriesString)) {
                    $filmCountries = explode(",", $getFilmInfo->countriesString);
                    foreach ($filmCountries as $k => $v) {



                        $couRepo = $em->getRepository('SqlSetupBundle:Country');
                        $countryAlreadyAdded = $couRepo->findOneByName($v);

                        if ($countryAlreadyAdded) {
                            $film->addCountry($countryAlreadyAdded);
                        }
                        else {
                            $country = new Country();
                            $country->setName($v);
                            $film->addCountry($country);
                            $em->persist($country);
                        }
                    }
                }

                try {
                    $em->persist($film);
                    $em->flush();
                } catch (PDOException $e) {
                    echo "Exception: " . $e->getMessage() . '<br>';
                }


                /*$c = curl_init();
                curl_setopt($c, CURLOPT_URL, $getFilmInfo->imagePath);
                curl_setopt($c, CURLOPT_HEADER, 0);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
                $result = curl_exec($c);
                curl_close($c);
                if ($result) {
                    $basedir = $_SERVER['DOCUMENT_ROOT'];
                    $picname = $basedir . '/obrazek' . $filmID . '.jpg';
                    file_put_contents($picname, $result);
                }*/

                echo "New film - id: " . $filmID . "<br>";

                return new Response('New film - id: ' . $film->getId());
        }
    }
}
