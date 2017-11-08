<?php
namespace Kino\Controller;

use Kino\Logger\CustomLoggerSingletonFactory;
use \Zend\Config\Config;
use \Zend\Http\Client;
use \Zend\Http\Request;
use \Zend\View\Model\JsonModel;

define("FNAME_OMDb_API_CONFIG_DEV", "omdb.api.config.php");
define("SAMPLE_SEARCH_STRING", "Star Trek");

class MovieRestfulController extends RestfulControllerTemplate
{
    protected $commentsTable;
    protected $ratingsTable;

    private $env;
    private $logger;
    private $client;
    private $omdbApiConfigFileName;

    public function __construct()
    {
        // retreive application environment variable and store it locally
        $this->env = getenv('APPLICATION_ENV');
        
        $this->logger = CustomLoggerSingletonFactory::Logger();
        $this->logger->debug("Currently using environment '" . $this->env . "'.");
        
        if ($this->env == 'production') { // customized for Heroku deployment - using Heroku app config variables
            $this->omdbApiConfig = new Config(array(
                'OMDb_API' => array(
                    'OMDb_API_ROOT' => getenv('OMDb_API_ROOT'),
                    'OMDb_API_KEY' => getenv('OMDb_API_KEY')
                )
            ));
        } else if ($this->env == 'development') { // using local config file in 'development'
                                                  // OMDb API config full name
            $envDevelopmentOmdbApiConfigPath = $_SERVER['DOCUMENT_ROOT'] . '/..' . '/module/Kino/config/' . constant("FNAME_OMDb_API_CONFIG_DEV");
            $this->omdbApiConfig = new Config(include $envDevelopmentOmdbApiConfigPath);
        }
        
        // replace default GET route id parameter name 'id' with 'imdbID'
        $this->identifierName = 'imdbID';
        
        // HTTP client setup
        $this->client = new Client($this->omdbApiConfig->OMDb_API->OMDb_API_ROOT, array(
            'maxredirects' => 0,
            'timeout' => 30
        ));
        
        $this->client->setHeaders(array(
            'Content-type' => 'application/json; charset=utf-8'
        ));
        
    }

    /**
     * Get list of movie models
     */
    public function getList()
    {
        $this->logger->debug("Inside MovieRestfulController, getList() HTTP request handler");
        
        $data = (array) $this->getRequest()->getQuery(); // return array
        if (! array_key_exists('s', $data)) {
            $data = array(
                "s" => constant("SAMPLE_SEARCH_STRING"), // constant("SAMPLE_SEARCH_STRING"),
                "apikey" => $this->omdbApiConfig->OMDb_API->OMDb_API_KEY
            );
        } else
            $data["apikey"] = $this->omdbApiConfig->OMDb_API->OMDb_API_KEY;
        
        $this->client->resetParameters($clearCookies = false);
        
        $this->client->setMethod(Request::METHOD_GET);
        $this->client->setParameterGet($data);
        $this->client->send();
        $response = $this->client->getResponse();
        
        $fixtureModelJson = "{\"Search\":[{\"Title\":\"Star Trek\",\"Year\":\"2009\",\"imdbID\":\"tt0796366\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMjE5NDQ5OTE4Ml5BMl5BanBnXkFtZTcwOTE3NDIzMw@@._V1_SX300.jpg\"},{\"Title\":\"Star Trek: Into Darkness\",\"Year\":\"2013\",\"imdbID\":\"tt1408101\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMTk2NzczOTgxNF5BMl5BanBnXkFtZTcwODQ5ODczOQ@@._V1_SX300.jpg\"},{\"Title\":\"Star Trek: Beyond\",\"Year\":\"2016\",\"imdbID\":\"tt2660888\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BZDRiOGE5ZTctOWIxOS00MWQwLThlMDYtNWIwMDQwNzBjZDY1XkEyXkFqcGdeQXVyNjU0OTQ0OTY@._V1_SX300.jpg\"},{\"Title\":\"Star Trek II: The Wrath of Khan\",\"Year\":\"1982\",\"imdbID\":\"tt0084726\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMzcyYWE5YmQtNDE1Yi00ZjlmLWFlZTAtMzRjODBiYjM3OTA3XkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg\"},{\"Title\":\"Star Trek: The Next Generation\",\"Year\":\"19871994\",\"imdbID\":\"tt0092455\",\"Type\":\"series\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BNDViYjAyZWUtNGQxMy00MDUyLTlkZTAtOWNkY2M5ZTk5MTE5XkEyXkFqcGdeQXVyNTA4NzY1MzY@._V1_SX300.jpg\"},{\"Title\":\"Star Trek: The Motion Picture\",\"Year\":\"1979\",\"imdbID\":\"tt0079945\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BNzNlMzNlNmQtNmYzNS00YmU5LWIzYWQtMDRkYzIzNzEzOTIyXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg\"},{\"Title\":\"Star Trek IV: The Voyage Home\",\"Year\":\"1986\",\"imdbID\":\"tt0092007\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMGY2MDE2MGQtMjczYi00YTdhLWIzNzktNDk2NzMzZmYwMTJjXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg\"},{\"Title\":\"Star Trek: Generations\",\"Year\":\"1994\",\"imdbID\":\"tt0111280\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BNjFiMzc4YzAtNGMzYS00NjI0LWJhYTYtN2JiOTI2ODczYzE3XkEyXkFqcGdeQXVyNTUyMzE4Mzg@._V1_SX300.jpg\"},{\"Title\":\"Star Trek III: The Search for Spock\",\"Year\":\"1984\",\"imdbID\":\"tt0088170\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMTliZGVjZmMtNzEzMy00MzVhLWFhYjYtNDhlYmViNGNiMGFlXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_SX300.jpg\"},{\"Title\":\"Star Trek: Nemesis\",\"Year\":\"2002\",\"imdbID\":\"tt0253754\",\"Type\":\"movie\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMjAxNjY2NDY3NF5BMl5BanBnXkFtZTcwMjA0MTEzMw@@._V1_SX300.jpg\"}],\"totalResults\":\"285\",\"Response\":\"True\"}";
        
        if ($response->isSuccess()) {
            return new JsonModel(json_decode($response->getBody(), true)["Search"]);
        } else {
            if ($this->env == 'development') { // temporary solution - using a fixture when movie API is unavailable
                return new JsonModel(json_decode($fixtureModelJson, true)["Search"]);
            }
        }
    }

    /**
     * Get single movie model by id
     *
     * @param mixed $id
     * @return mixed
     */
    public function get($imdbID)
    {
        $this->logger->debug("Inside MovieRestfulController, get() HTTP request handler");
        $this->logger->debug("\$imdbID: " . $imdbID);
        
        $data = array(
            "i" => $imdbID,
            "apikey" => $this->omdbApiConfig->OMDb_API->OMDb_API_KEY
        );
        
        $this->client->resetParameters($clearCookies = false);
        
        $this->client->setMethod(Request::METHOD_GET);
        $this->client->setParameterGet($data);
        $this->client->send();
        
        $response = $this->client->getResponse();
        
        if ($response->isSuccess()) {
            return new JsonModel(json_decode($response->getBody(), true));
        } else {
            if ($this->env == 'development') { // temporary solution - using a fixture when movie API is unavailable
                $fixtureModelJson = "{\"Title\":\"Guardians of the Galaxy Vol. 2\",\"Year\":\"2017\",\"Rated\":\"PG-13\",\"Released\":\"05 May 2017\",\"Runtime\":\"136 min\",\"Genre\":\"Action, Adventure, Sci-Fi\",\"Director\":\"James Gunn\",\"Writer\":\"James Gunn, Dan Abnett (based on the Marvel comics by), Andy Lanning (based on the Marvel comics by), Steve Englehart (Star-lord created by), Steve Gan (Star-lord created by), Jim Starlin (Gamora and Drax created by), Stan Lee (Groot created by), Larry Lieber (Groot created by), Jack Kirby (Groot created by), Bill Mantlo (Rocket Raccoon created by), Keith Giffen (Rocket Raccoon created by), Steve Gerber (character created by: Howard the Duck), Val Mayerik (character created by: Howard the Duck)\",\"Actors\":\"Chris Pratt, Zoe Saldana, Dave Bautista, Vin Diesel\",\"Plot\":\"The Guardians must fight to keep their newfound family together as they unravel the mystery of Peter Quill's true parentage.\",\"Language\":\"English\",\"Country\":\"USA, New Zealand, Canada\",\"Awards\":\"4 wins & 10 nominations.\",\"Poster\":\"https:\/\/images-na.ssl-images-amazon.com\/images\/M\/MV5BMTg2MzI1MTg3OF5BMl5BanBnXkFtZTgwNTU3NDA2MTI@._V1_SX300.jpg\",\"Ratings\":[{\"Source\":\"Internet Movie Database\",\"Value\":\"7.8\/10\"},{\"Source\":\"Rotten Tomatoes\",\"Value\":\"82%\"},{\"Source\":\"Metacritic\",\"Value\":\"67\/100\"}],\"Metascore\":\"67\",\"imdbRating\":\"7.8\",\"imdbVotes\":\"271,505\",\"imdbID\":\"tt3896198\",\"Type\":\"movie\",\"DVD\":\"22 Aug 2017\",\"BoxOffice\":\"$389,804,217\",\"Production\":\"Walt Disney Pictures\",\"Website\":\"https:\/\/marvel.com\/guardians\",\"Response\":\"True\"}";
                
                // quick fix for imdbRating value
                $json = json_decode($fixtureModelJson, true);
                $json['imdbRating'] = (float) $json['imdbRating'];
                
                return new JsonModel($json);
            }
        }
    }
    
    // public function getCommentsTable ()
    // {
    // if (! $this->commentsTable) {
    // $sm = $this->getServiceLocator();
    // $this->commentsTable = $sm->get('Kino\Model\CommentTable');
    // }
    // return $this->commentsTable;
    // }
}

?>