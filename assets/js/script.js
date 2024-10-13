

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);



$(document).ready(function() {
    
    
    var fetchGenresUrl = $('#pathGenre').data('url');

    var genresArray = [];



        $.ajax({
            url: fetchGenresUrl, // Remplacez par l'URL de votre API
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#filtreGenre').empty(); // Vider le conteneur avant d'ajouter de nouveaux résultats
                $.each(data.genres, function(index, genre) {
                    genresArray[genre.id] = genre.name;
                    $('#filtreGenre').append(
                        '<tr><td><label class="genres"><input type="checkbox"  name="genres"  value="'+genre.id+'">'+genre.name+'</label></td></tr>'
                    );
                });

                $('input[type="checkbox"][name="genres"]').change(function() {
                    filtreMovie();
                });
            },
            error: function(xhr, status, error) {
                console.error('Une erreur est survenue : ' + error);
                $('#results').html('<p>Erreur de chargement des genres.</p>');
            }
        });




        filtreMovie();



        $('#precedent').on('click', function(event) {
            event.preventDefault(); // Empêche le comportement par défaut du lien
            var currentPage = $('#currentPage').data('page');
            currentPage = currentPage - 1;
            filtreMovie(currentPage);
        });




        $('#suivant').on('click', function(event) {
            event.preventDefault(); // Empêche le comportement par défaut du lien
            var currentPage = $('#currentPage').data('page');
            currentPage = currentPage + 1 ;
            filtreMovie(currentPage);
        });




    function filtreMovie(currentPage =1){
            let selectedGenres = [];
            $('input[type="checkbox"][name="genres"]:checked').each(function() {
                selectedGenres.push($(this).val());
            });
            getMovies(currentPage,selectedGenres.join('|'));        
    }




   function getMovies(currentPage = 1, genres = ''){
        
        var fetchMoviesUrl = Routing.generate('movies', { 'page': currentPage, 'genres': genres });
        $.ajax({
            url: fetchMoviesUrl,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('.movie-list').empty();
                $.each(data, function(index, movie) {
                     addMovie(movie);   
                });
                $('#currentPage').data('page',currentPage);
                if(currentPage == 1 ){
                    $('#precedent').css('display', 'none');
                }else{if(currentPage > 1 ){
                    $('#precedent').css('display', 'block');
                }}

                
                $('html, body').animate({ scrollTop: 0 }, 800);
            },
            error: function(xhr, status, error) {
                console.error('Une erreur est survenue : ' + error);
                $('#results').html('<p>Erreur de chargement des films.</p>');
            }
        });
   }


   function addMovie(movie){
            let genresMovie = "";
            if(typeof movie.genre_ids !== 'undefined'){
                movie.genre_ids.forEach(id => {
                    genresMovie += genresArray[id] + ", ";
                });
            }else{
                movie.genres.forEach(genre => {
                    genresMovie += genre.name + ", ";
                });
            }
             genresMovie = genresMovie.slice(0, -2);

            $('.movie-list').append(
                '<li class="movie-item">' +
                        '<img src="https://image.tmdb.org/t/p/w400/'+ movie.backdrop_path+ '" alt="'+movie.original_title+'">' +
                        '<table><tr><td><strong>'+movie.original_title+'</strong> ('+movie.vote_count+' votes)</td></tr>' +
                        '<tr><td>'+movie.release_date+'</td></tr>' +
                        '<tr><td><p>'+movie.overview+'</p></td></tr>' +
                        '<tr><td><p>'+genresMovie+'</p></td></tr>' +
                        '<tr><td><a href="#" id="openPopup-'+movie.id+'" data-movie-id="'+movie.id+'" data-video-id="'+movie.videoo.key+'"  class="button">Lire le détail</a></td></tr></table>' +
                        '</li>'
            );

        
        $('#openPopup-'+movie.id).click(function(event) {
            event.preventDefault(); // Empêche le comportement par défaut du lien
                        
            const movieId = $(this).data('movie-id');
            const videoId = $(this).data('video-id');

            var video;
            if (videoId != 'undefined') {
                video = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe>';
            } else {
                video = '<img src="https://image.tmdb.org/t/p/w500/'+ movie.backdrop_path+ '" alt="'+movie.original_title+'">' ;

            }
            

            // Créer le contenu du popup
            $('#popup').append(
                '<div id="videoPopup-' + movieId + '" class="popup">' +
                    '<div class="popup-content">' +
                        '<span class="close">&times;</span>' + video +
                        '<table><tr><td><strong>'+movie.original_title+'</strong> ('+movie.vote_count+' votes)</td></tr>' +
                        '<tr><td>'+movie.release_date+'</td></tr>' +
                        '<tr><td><p>'+movie.overview+'</p></td></tr>' +
                        '<tr><td><p>'+genresMovie+'</p></td></tr>' +
                        '</table>'+
                    '</div>' +
                '</div>'
            );

            // Afficher le popup
            $('#videoPopup-' + movieId).fadeIn();
        });

        // Gérer la fermeture du popup
        $(document).on('click', '.close', function() {
            $(this).closest('.popup').fadeOut(function() {
                $(this).remove(); // Supprimer le popup du DOM
            });
        });

        $(window).click(function(event) {
            if ($(event.target).hasClass('popup')) {
                $(event.target).fadeOut(function() {
                    $(this).remove(); // Supprimer le popup du DOM
                });
            }
        });
   }
   

  


   /*    AutoComplete    */
   $("#autocomplete").autocomplete({
    source: function(request, response) {
      var searchURL = Routing.generate('autocomplete_search', { 'query': request.term });
      $.ajax({
        url: searchURL , 
        dataType: "json",
        success: function(data) {
            var movies = data.map(function(movie) {
                return {
                    label: movie.name, // Ce qui s'affiche dans la liste
                    id: movie.id // L'ID de l'élément
                };
            });
            response(movies);
        }
      });
    },
    minLength: 2,
    select: function(event, ui) {
        var selectedId = ui.item.id;
        
        // Effectuez ici la recherche par ID, par exemple, redirigez ou récupérez des détails
        //////////////////
        var getMovieUrl = Routing.generate('get_movie', { 'movie_id': selectedId});
        $.ajax({
            url: getMovieUrl,
            method: 'GET',
            dataType: 'json',
            success: function(movie) {
                $('.movie-list').empty();
                addMovie(movie);

                $('#currentPage').data('page',currentPage);
                if(currentPage == 1 ){
                    $('#precedent').css('display', 'none');
                }else{if(currentPage > 1 ){
                    $('#precedent').css('display', 'block');
                }}

                $('html, body').animate({ scrollTop: 0 }, 800);

            },
            error: function(xhr, status, error) {
                console.error('Une erreur est survenue : ' + error);
                $('#results').html('<p>Erreur de chargement des films.</p>');
            }
        });     
    }
  });
  /*    Fin AutoComplete    */


 
  

    

});


