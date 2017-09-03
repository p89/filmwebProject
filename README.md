filmwebProject
==============

filmwebProject is the final project for coders's lab workshops. It consists of three parts:
 
 1. The parser of filmweb.pl database based on open mobile API.
 2. RestAPI using JSON available at /getFilm/films.{_format}
 3. Deployment on the mobile server running linux (I used Sony Xperia Z3 Compact, resulting in compact :), waterproof (not really), battery powered server.
 
 
 ### 1. The Parser
 
 The parser is based on the abandoned project by [b44x](https://github.com/b44x/filmweb-api). It used filmweb mobile API available for logged user. I've adjusted the request content for this project and mapped response to SQL tables using Doctrine's DBAL. The film database is not available on github, however you can get your own :). The parser can be used both from bash or from web-browser of your choice (I recommend Firefox). 
 
 #### Setup
 
Firstly, you want to fire the following: 
```
$ git clone https://github.com/p89/filmwebProject.git
$ cd filmwebProject
$ composer install
```
After the packages are ready go to app/config/parameters.yml and set the following parameters:
```
database_host: 127.0.0.1
database_port: null
database_name: dbToStoreMovies
database_user: user
database_password: password
```
 Then you need to go to [filmweb.pl](http://filmweb.pl) and create free account and the login credentials to as two extra parameters in the parameters.yml
```
filmweb_login: yourLogin
filmweb_pass: yourPass
```
 Great, three things left. If you're going to use the parser from web-browser e.g Firefox, chances are your connection might be killed after few minutes due to the browser and server settings. In order to prevent this (please note you might need to adjust user access to conf files to edit them):
  * in Firefox, go to about:config address and change **http.response.timeout** to some large number like **60000**
  * in server configuration file (path for Apache) Config\Apache\extra\httpd-default.conf set **Timeout 60000** 
  * in PHP.ini file Config\Php\php.ini set **max_execution_time = 300**
  
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
 ### 4. Extras: SQL optimalization 
 
 
 
