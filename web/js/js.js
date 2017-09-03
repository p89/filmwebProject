$(function() {

    let minYear = 1970;
    let maxYear = 2017;
    let minRating = 7;
    let maxRating = 10;
    let minVotes = 10;
    let maxVotes = 900000;

    let genres = ['Akcja', 'Animacja', 'Anime', 'Baśń','Biblijny', 'Biograficzny', 'Czarna komedia', 'Dla dzieci', 'Dla młodzieży', 'Dokumentalizowany',
     'Dokumentalny',
     'Dramat',
     'Dramat historyczny',
     'Dramat obyczajowy',
     'Dramat sądowy',
     'Dramat społeczny',
     'Dreszczowiec',
     'Edukacyjny',
     'Erotyczny',
     'Etiuda',
     'Fabularyzowany dok.',
     'Familijny',
     'Fantasy',
     'Fikcja literacka',
     'Film-Noir',
     'Fitness',
     'Gangsterski',
     'Groteska filmowa',
     'Hazard',
     'Historyczny',
     'Horror',
     'Karate',
     'Katastroficzny',
     'Komedia',
     'Komedia dokumentalna',
     'Komedia kryminalna',
     'Komedia obycz.',
     'Komedia rom.',
     'Kostiumowy',
     'Krótkometrażowy',
     'Kryminał',
     'Melodramat',
     'Menedżer',
     'Musical',
     'Muzyczna',
     'Muzyczny',
     'Niemy',
     'Nowele filmowe',
     'Obyczajowy',
     'Płaszcza i szpady',
     'Poetycki',
     'Polityczny',
     'Pornografia',
     'Prawniczy',
     'Propagandowy',
     'Przygodowy',
     'Przyrodniczy', 'Psychologiczny', 'Religijny', 'Romans', 'Satyra', 'Sci-Fi', 'Sensacyjny', 'Sportowy', 'Surrealistyczny', 'Szpiegowski', 'Sztuki walki', 'Thriller', 'Western', 'Wojenny', 'XXX'];

    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            matches = [];
            substrRegex = new RegExp(q, 'i');

            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });
            cb(matches);
        };
    };

    $('#filterFields .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'genres',
            source: substringMatcher(genres)
        });

    $("#yearRange").ionRangeSlider({
        type: "double",
        min: 1890,
        max: 2020,
        from: 1970,
        to: 2017,
        step: 1,
        onStart: function (data) {
            minYear = data.from;
            maxYear = data.to;
        },
        onChange: function (data) {
            minYear = data.from;
            maxYear = data.to;
        },
        onFinish: function (data) {
            minYear = data.from;
            maxYear = data.to;
        },
        onUpdate: function (data) {
            minYear = data.from;
            maxYear = data.to;
        }
    });

    $("#Rating").ionRangeSlider({
        type: "double",
        min: 1,
        max: 10,
        from: 7,
        to: 10,
        step: 1,
        onStart: function (data) {
            minRating = data.from;
            maxRating = data.to;
        },
        onChange: function (data) {
            minRating = data.from;
            maxRating = data.to;
        },
        onFinish: function (data) {
            minRating = data.from;
            maxRating = data.to;
        },
        onUpdate: function (data) {
            minRating = data.from;
            maxRating = data.to;
        }
    });

    $("#votesRange").ionRangeSlider({
        type: "double",
        min: 0,
        max: 900000,
        from: 10000,
        to: 900000,
        step: 1000,
        onStart: function (data) {
            minVotes = data.from;
            maxVotes = data.to;
        },
        onChange: function (data) {
            minVotes = data.from;
            maxVotes = data.to;
        },
        onFinish: function (data) {
            minVotes = data.from;
            maxVotes = data.to;
        },
        onUpdate: function (data) {
            minVotes = data.from;
            maxVotes = data.to;
        }
    });


    $('.random').on('click', function (event) {
        event.preventDefault();

        let filmGenre = $("#filmGenreFilter").val();

        film = {
            minYear: minYear,
            maxYear: maxYear,
            minRating: minRating,
            maxRating: maxRating,
            minVotes: minVotes,
            maxVotes: maxVotes,
            filmGenre: filmGenre
        };

        console.log(film);

        $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ('/getFilm/films'),
                data: film
            })
            .done(function (response) {


                let data = JSON.parse(response);

                console.log(data);
                if (data['error']) {
                    alert(data['error']);
                } else {

                    let review = data['filmReview'] ? data['filmReview'] : "Brak recenzji.";
                    let description = data['filmDesc'] ? data['filmDesc'] : "Brak opisu.";
                    let year = data['filmYear'] ? data['filmYear'] : "Nieznany";
                    let cover = `<img class="poster" src="covers/${data['coverSrc']}">`;

                    if (data['filmGenre'].length > 0) {
                        genres = "•";
                        for (let i = 0; i < data['filmGenre'].length; i++) {
                            genres += data['filmGenre'][i] + " •";
                        }
                    } else {
                        genres = "Brak gatunku";
                    }

                    if (data['filmCountry'].length > 0) {
                        countries = "•";
                        for (let i = 0; i < data['filmCountry'].length; i++) {
                            countries += data['filmCountry'][i] + " •";
                        }
                    } else {
                        countries = "";
                    }

                    if (data['filmDirector'].length > 0) {
                        directors = "";
                        for (let i = 0; i < data['filmDirector'].length; i++) {
                            directors += "<li>" + data['filmDirector'][i] + "</li>";
                        }
                    } else {
                        directors = "";
                    }

                    if (data['filmScreenwriter'].length > 0) {
                        screenwriters = "";
                        for (let i = 0; i < data['filmScreenwriter'].length; i++) {
                            screenwriters += "<li>" + data['filmScreenwriter'][i] + "</li>";
                        }
                    } else {
                        screenwriters = "";
                    }

                    $('.mainImage').html(cover);
                    $('.filmPlTitle').text(data['filmPlTitle']);
                    $('.filmRating').text('Ocena: ' + data['filmRating']);
                    $('.filmEnTitle').text(data['filmEnTitle']);
                    $('.filmYear').text(year + " " + countries);
                    $('.filmDesc').text(description);
                    $('.filmReview').text(review);
                    $('.filmGenre').html(genres);
                    $('.filmDirector').html(directors);
                    $('.filmScreenwriter').html(screenwriters);
                }

            })
            .fail(function (error) {
                console.log('Server response error:', error);
            });
    });
});

