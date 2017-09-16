filmwebProject
==============

filmwebProject is the final project for [coders's lab](https://github.com/CodersLab) workshops. It consists of three parts:
 
 1. The parser of filmweb.pl database based on open mobile API.
 2. RestAPI using JSON
 3. Deployment on the mobile server running linux (I used Sony Xperia Z3 Compact, resulting in compact :), waterproof (not really), battery powered server.
 
 
 ### 1. The Parser
 
 The parser is based on the abandoned project by [b44x](https://github.com/b44x/filmweb-api). It used filmweb mobile API available for logged user. I've adjusted the request content for this project and mapped response to SQL tables using Doctrine's DBAL. The film database is not available on github, however you can get your own :). The parser can be used both from bash or from web-browser of your choice (I recommend Firefox). 
 
 #### Setup
 
Firstly, you want to execute the following: 
```
$ git clone https://github.com/p89/filmwebProject.git
$ cd filmwebProject
$ composer install
```
After the packages are ready, go to app/config/parameters.yml and set the following parameters:
```
database_host: 127.0.0.1
database_port: null
database_name: dbToStoreMovies
database_user: user
database_password: password
```
 Then visit [filmweb.pl](http://filmweb.pl) and create free account and add the login credentials to parameters.yml as two extra lines: 
```
filmweb_login: yourLogin
filmweb_pass: yourPass
```
 Great, three things left. If you're going to use the parser from web-browser e.g Firefox, chances are your connection might be killed after few minutes due to the browser and server settings. In order to prevent this (please note you might need to adjust user access to conf files to edit them):
  * in Firefox, go to about:config address and change **http.response.timeout** to some large number like **60000**
  * in server configuration file (path for Apache) Config\Apache\extra\httpd-default.conf set **Timeout 60000** 
  * in PHP.ini file Config\Php\php.ini set **max_execution_time = 60000**
  
  Done!
  
  #### How to use
  
  It's really simple. Firstly, generate the tables and dependencies by running schema update and check the validation just in case:
  
```
$ bin/console doctrine:schema:update --force
$ bin/console doctrine:schema:validate
```
 Now everything you need to do is to visit the following address:
 
 ```
 /parser/{startID}/{endID}
 ```
 Where the enclosed variables are the filmIDs from the 0 to ~900 000 range.
 
 The parser efficiency reaches about 20 records a second when reconfigured to store images as hardlinks inserted to SQL table or about 2-3 records when it's set to save the image on hard drive. Please note that complete database needs about 100GB of storage (1% SQL data, 99% images). 
 
 ### 2. RestAPI
 
 Currently the API offers only one endpoint available at available at /getFilm/films.{_format} which responds with the complete data of searched movie. 
 
 RestAPI example call:
 ```
  {
      minYear: 1970,
      maxYear: 2017,
      minRating: 7,
      maxRating: 10,
      minVotes: 10000
      filmGenre: ""
  }
 ``` 
 Example response:
 
 ```
 {
     coverSrc : "nopicture.gif"
     filmCountry : Array(1) 
         0 : "USA" 
         length : 1
     
     filmDesc : "Description."
     filmDirector : Array(1)
         0 : "Richard Linklater"
         length : 1
     
     filmEnTitle : "A Scanner Darkly"
     filmGenre : Array(3)
         0 : "Thriller"
         1 : "Animacja"
         2 : "Sci-Fi"
         length : 3
     
     filmPlTitle : "Przez ciemne zwierciadło"
     filmRating : 7.2
     filmReview : "Review"
     filmScreenwriter : Array(1)
         0 : "Richard Linklater"
         length : 1
     
     filmYear: 2006
 }
 
 ```
 The variables are self-explanatory. The list of valid filmGenre parameters can be found in the js.js file.
 
 ### 3. Ubuntu mobile webserver
 
 I describe the process of deployment a LAMP stack on an Android phone in a 2-part article, which can be found on my blog:
 
 http://prochal.com/2017/09/turn-your-android-phone-into-a-web-server/
 
 http://prochal.com/2017/09/turn-android-smartphone-linux-web-server-part-2/
 
 ### 4. Extras: SQL optimalization 
 
 Given the application will run on a Snapdragon, performance  seemed to be an issue even during development, when working on a database of 1/10th the final size. Query was improved in three iterations - from total disaster to an acceptable result.
 
 #### Problem
 
 It turns out filmweb's database contains lots of movies with no data (votes, description, genres etc.) plus, surprisingly, also computer games. I got rid of those by running few simple queries.
 
 As a result database lost ~60% of its results, creating gaps which meant it was no longer possible to choose film by random in the easy way i.e.
 
 ```php
 $em->getRepository('SqlSetupBundle:Film')->findOneById(rand(minID,maxID));
 ```
 ##### 1st solution
 
 The simplest turnaround for dev was to fetch all movies and then pick one by random
 
 ```
 $films = $em->getRepository('SqlSetupBundle:Film')->findAll();
 $film = $films[rand(0, count($films)];
 ```
 The query took over 5 seconds on i7 Core to execute and way beyond 500MB of RAM - duh.
  
 ##### 2nd solution
  
 I opted to use native SQL query, with the standardized rand() function. I also added WHERE clauses for AJAX calls on button click: 
 ```sql
 SELECT f1.id
    FROM film AS f1
    JOIN film_genre fg on fg.film_id = f1.id
    JOIN genre g on fg.genre_id = g.id
    WHERE g.Name = COALESCE(NULLIF(:filmGenre, ''), g.Name)
            AND Rating >= :minRating
            AND Rating <= :maxRating
            AND Year >= :minYear
            AND Year <= :maxYear
            AND Votes <= :maxVotes
            AND Votes >= :minVotes
    ORDER BY rand() ASC
    LIMIT 1
```
**1.95s** to execute, a bit better, but still unacceptable. Also rather than adding another IF statement in php, I reached out for the optional parameter construct  

```sql
WHERE g.Name = COALESCE(NULLIF(:filmGenre, ''), g.Name)
```
The poor performance of this built-in feature lies in table scan necessary to order by random values. It cannot be turned into binary search (indexed) and capitalize the O(log n) complexity, ending up with linear complexity O(n).

 ##### 3rd & Final solution
 
 There are many approaches on how to optimize such query. One of the more popular solution is to use a query like (in this case we order by indexed column and use rand() only to generate one input):
 
 ```sql
SELECT name
  FROM random AS r1 JOIN
       (SELECT (RAND() *
                     (SELECT MAX(id)
                        FROM random)) AS id)
        AS r2
 WHERE r1.id >= r2.id
 ORDER BY r1.id ASC
 LIMIT 1;
```

However, there are two issues with that:

1. It doesn't work when you have gaps in the table
2. It can seldomly return empty result after adding filtering WHERE clause (a case where the selected MAX(ID) does not meet the filtering criteria from the outer query) even when table has no gaps.

Ok, let's fine-tune it. Firstly, I've created another column with sequential number, to get rid of gaps.

```
SELECT @seqnum:=0;
UPDATE film SET seqnum = @seqnum:=@seqnum+1;
```

The final step was to write a query in such a way that it won't ever return empty results, while remaining efficient.

Here it is:

```sql
SELECT f1.seqnum
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
    LIMIT 1
```

**0.04s** on average, way better plus thanks to using double filters this won't return empty set. The random distribution is not great when dealing with a small sample (<10), but acceptable. 

If you come thus far, thanks for reading! :)