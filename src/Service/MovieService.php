<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;

class MovieService
{
    private $client;
    private string $apiKey;


    public function __construct(string $apiKey)
    {
        $this->client = HttpClient::create();
        $this->apiKey = $apiKey;
    }

    public function getGenres(): array
    {
        try {
            $response = $this->client->request('GET', 'https://api.themoviedb.org/3/genre/movie/list?language=fr', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'api_key' => $this->apiKey ,
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->toArray();
            }
        } catch (TransportException $e) {
            // Gérer les erreurs de transport
            return ['error' => 'API call failed: ' . $e->getMessage()];
        }

        return ['error' => 'API call failed'];
    }


    public function getMovies($page,$genre = ""): array
    {
        try {

            $response = $this->client->request('GET', 'https://api.themoviedb.org/3/discover/movie', [
                'query' => [
                    'api_key' => $this->apiKey ,
                    'include_adult' => 'true',
                    'include_video' => 'true', // Cela peut ne pas filtrer comme attendu
                    'language' => 'fr',
                    'per_page' => 5,
                    'page' => $page,
                    'sort_by' => 'popularity.desc',
                    'with_genres' => $genre, // Remplacez par les genres souhaités
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $movies = $response->toArray()["results"];
                for($i=0;$i<count($movies);$i++){
                    $video = $this->getVideo($movies[$i]['id']);
                    if(count($video)> 0){
                        $movies[$i]['videoo'] = $video[0];
                    }else{
                        $movies[$i]['videoo'] =[];
                    }
                    
                }
                return $movies;
                
                // return $response->toArray();
            }
        } catch (TransportException $e) {
            // Gérer les erreurs de transport
            return ['error' => 'API call failed: ' . $e->getMessage()];
        }

        return ['error' => 'API call failed'];
    }

    public function getTopMovie(): array
    {
        try {

            $response = $this->client->request('GET', 'https://api.themoviedb.org/3/movie/top_rated', [
                'query' => [
                    'api_key' => $this->apiKey ,
                    'language' => 'fr',
                    'page' => 1,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $movies = $response->toArray()["results"];
                $movie = $movies[0];
                $video = $this->getVideo($movie['id']);
                $movie['videoo'] = $video;
                return $movie;
            }
        } catch (TransportException $e) {
            // Gérer les erreurs de transport
            return ['error' => 'API call failed: ' . $e->getMessage()];
        }

        return ['error' => 'API call failed'];
    }

    public function searchAutocomplete($search)
    {
        try {

            $response = $this->client->request('GET', 'https://api.themoviedb.org/3/search/movie', [
                'query' => [
                    'api_key' => $this->apiKey ,
                    'language' => 'fr',
                    'query' => $search,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $movies = $response->toArray()["results"];
                $moviesRes = array();
                foreach($movies as $movie){
                    $moviesRes[] = ['id'=> $movie['id'], 'name'=>$movie['original_title']];
                }
               // dd($title);
                return $moviesRes;
            }
        } catch (TransportException $e) {
            // Gérer les erreurs de transport
            return ['error' => 'API call failed: ' . $e->getMessage()];
        }

        return ['error' => 'API call failed'];
    }

    public function getMovie($movieId)
    {
        try {

            $response = $this->client->request('GET', 'https://api.themoviedb.org/3/movie/'.$movieId, [
                'query' => [
                    'api_key' => $this->apiKey ,
                    'language' => 'fr',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                    $movie = $response->toArray();
                    $video = $this->getVideo($movie['id']);
                    if(count($video)> 0){
                        $movie['videoo'] = $video[0];
                    }else{
                        $movie['videoo'] =[];
                    }
                    
                }
                return $movie;
             //   return $response->toArray();
        } catch (TransportException $e) {
            // Gérer les erreurs de transport
            return ['error' => 'API call failed: ' . $e->getMessage()];
        }

        return ['error' => 'API call failed'];
    }
    
    
    

    public function getVideo($movieId)
    {
        try {
            $video = array();
            $videoResponse = $this->client->request('GET', "https://api.themoviedb.org/3/movie/{$movieId}/videos", [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'fr',
                ],
            ]);
    
            if ($videoResponse->getStatusCode() === 200) {
                return $videoResponse->toArray()['results'];
            }
        } catch (TransportException $e) {
            // Gérer les erreurs de transport
            return ['error' => 'API call failed: ' . $e->getMessage()];
        }

    }


}
