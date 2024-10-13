<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MovieService;
use Symfony\Component\HttpFoundation\Response;


class MovieController  extends AbstractController
{

    private $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    #[Route('/{page}', name: 'index', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function index($page): Response
    {
        $topMovie = $this->movieService->getTopMovie();
        
        return $this->render('movie/index.html.twig', [
            'current_page' => $page,
            'topMovie' => $topMovie
        ]);
    }


   
    #[Route('/api/genre-movie', name: 'movie_genre')]
    public function genreMovie(): JsonResponse
    {
        $genres = $this->movieService->getGenres();

        return new JsonResponse($genres);
    }




    #[Route('/api/movies/{page}/{genres}', name: 'movies', requirements: ['page' => '\d+'], defaults: ['page' => 1, 'genres'=> ''],options: ['expose' => true])]
    public function movies($page,$genres): JsonResponse
    {
        $movies = $this->movieService->getMovies($page,$genres);

        return new JsonResponse($movies);
    }

    #[Route('/api/search/{query}', name: 'autocomplete_search', options: ['expose' => true])]
    public function autocompleteSearch($query): JsonResponse
    {
        $titlesMovie = $this->movieService->searchAutocomplete($query);

        return new JsonResponse($titlesMovie);
    }



    #[Route('/api/get/{movie_id}', name: 'get_movie', requirements: ['movie_id' => '\d+'],options: ['expose' => true])]
    public function getMovie($movie_id): JsonResponse
    {
        $movie = $this->movieService->getMovie($movie_id);

        return new JsonResponse($movie);
    }

}
