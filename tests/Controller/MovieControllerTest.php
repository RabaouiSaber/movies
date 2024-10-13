<?php

namespace App\Tests\Controller;

use App\Controller\MovieController;
use App\Service\MovieService; // Adjust this based on your actual namespace
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class MovieControllerTest  extends WebTestCase
{
    private $movieService;

    protected function setUp(): void
    {
        // Create a mock for the MovieService
        $this->movieService = $this->createMock(MovieService::class);
    }

    
    public function testGenreMovieReturnsJsonResponse()
    {
        // Arrange
        $this->movieService
            ->method('getGenres')
            ->willReturn(['Action', 'Comedy', 'Drama']); // Mocked data

        // Create the controller with the mocked MovieService
        $controller = new MovieController($this->movieService);
        
        // Act
        $response = $controller->genreMovie();

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        // Check the content of the JSON response
        $this->assertJsonStringEqualsJsonString(
            json_encode(['Action', 'Comedy', 'Drama']),
            $response->getContent()
        );
    }
    

    public function testMoviesReturnsJsonResponse()
    {
        // Arrange
        $page = 1;
        $genres = 'action';
        $mockedMovies = [
            ['title' => 'Action Movie 1', 'genre' => 'action'],
            ['title' => 'Action Movie 2', 'genre' => 'action'],
        ];

        $this->movieService
            ->method('getMovies')
            ->with($page, $genres)
            ->willReturn($mockedMovies); // Mocked data

        // Create the controller with the mocked MovieService
        $controller = new MovieController($this->movieService);
        
        // Act
        $response = $controller->movies($page, $genres);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // Check the content of the JSON response
        $this->assertJsonStringEqualsJsonString(
            json_encode($mockedMovies),
            $response->getContent()
        );
    }

    public function testAutocompleteSearchReturnsJsonResponse()
    {
        // Arrange
        $query = 'inception';
        $mockedTitles = ['Inception', 'Inception 2', 'Inception: The Beginning'];

        $this->movieService
            ->method('searchAutocomplete')
            ->with($query)
            ->willReturn($mockedTitles); // Mocked data

        // Create the controller with the mocked MovieService
        $controller = new MovieController($this->movieService);
        
        // Act
        $response = $controller->autocompleteSearch($query);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // Check the content of the JSON response
        $this->assertJsonStringEqualsJsonString(
            json_encode($mockedTitles),
            $response->getContent()
        );
    }


    public function testGetMovieReturnsJsonResponse()
    {
        // Arrange
        $movieId = 1;
        $mockedMovie = ['id' => 1, 'title' => 'Inception', 'genre' => 'Sci-Fi'];

        $this->movieService
            ->method('getMovie')
            ->with($movieId)
            ->willReturn($mockedMovie); // Mocked data

        // Create the controller with the mocked MovieService
        $controller = new MovieController($this->movieService);
        
        // Act
        $response = $controller->getMovie($movieId);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // Check the content of the JSON response
        $this->assertJsonStringEqualsJsonString(
            json_encode($mockedMovie),
            $response->getContent()
        );
    }

}
