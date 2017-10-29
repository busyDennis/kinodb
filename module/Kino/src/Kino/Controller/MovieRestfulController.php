<?php
namespace Kino\Controller;
use \Zend\Config\Config;
use \Zend\Http\Client;
use \Zend\Http\Request;
use \Zend\Log\Logger;
use \Zend\View\Model\JsonModel;

//error_reporting(E_ALL);
//ini_set("display_errors", 1);


if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));
define("FNAME_OMDb_API_CONFIG_DEV", "omdb.api.config.php");
define("FNAME_CUSTOM_LOG", "custom.log");
define("SAMPLE_SEARCH_STRING", "Star Trek");



class MovieRestfulController extends RestfulControllerTemplate
{
    protected $commentsTable;
    protected $ratingsTable;

    private $env;
    private $logger;
    private $client;
    private $omdbApiConfigFileName;
    
    public function __construct ()
    {
        // retreive application environment variable and store locally
        $this->env = getenv('APPLICATION_ENV');
        
        // logging
        fwrite(constant('STDERR'), "Current application environment setting is:");
        fwrite(constant('STDERR'), $this->env);
        
        if ($this->env == 'production') {
            fwrite(constant('STDERR'), 'env variable \'OMDb_API_ROOT\' takes value:  ');
            fwrite(constant('STDERR'), getenv('OMDb_API_ROOT'));
            fwrite(constant('STDERR'), 'env variable \'OMDb_API_KEY\' takes value:  ');
            fwrite(constant('STDERR'), getenv('OMDb_API_KEY'));
            
            
        }
        
        // ad hoc Eclipse IDE logger setup
        $this->logger = new Logger();
        
        $logDir = $_SERVER['DOCUMENT_ROOT'].'/..'.'/log/';
        // check if the log dir exists
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logWriteStream = fopen($logDir.constant("FNAME_CUSTOM_LOG"), "w+", false);
        $this->logger->addWriter('stream', null,
                array(
                        'stream' => $logWriteStream
                    )
            );
        
        // verifying that the logger is functional
        $this->logger->debug("Logger successfully configured with custom output file '".constant("FNAME_CUSTOM_LOG")."'.");
        $this->logger->debug("Currently using environment '".$this->env."'.");
        
        
        if ($this->env == 'production') { // customized for Heroku deployment - using Heroku app config variables
            $this->omdbApiConfig = new Config(array(
                'OMDb_API' => array(
                        'OMDb_API_ROOT' => getenv('OMDb_API_ROOT'),
                        'OMDb_API_KEY'  => getenv('OMDb_API_KEY')
                    )
                ));
        } else if ($this->env == 'development') { // using local config file in 'development'
            // OMDb API config full name
            $omdbApiConfigPath = $_SERVER['DOCUMENT_ROOT'].'/..'.'/module/Kino/config/'.constant("FNAME_OMDb_API_CONFIG_DEV");
            $this->omdbApiConfig = new Config(include $omdbApiConfigPath);
        }       
        
        // HTTP client setup
        $this->client = new Client($this->omdbApiConfig->OMDb_API->OMDb_API_ROOT,
                array(
                        'maxredirects' => 0,
                        'timeout' => 30
                ));

        $this->client->setHeaders(
                array(
                        'Content-type' => 'application/json; charset=utf-8'
                ));

        $this->client->setMethod(Request::METHOD_GET);
    }

    /**
     * Get list of movie models
     */
    public function getList ()
    {
        $data = (array) $this->getRequest()->getQuery(); // return array
        if (! array_key_exists('s', $data)) {
            $data = array(
                "s" => constant("SAMPLE_SEARCH_STRING"),
                "apikey" => $this->omdbApiConfig->OMDb_API->OMDb_API_KEY
            );
        } else
            $data["apikey"] = $this->omdbApiConfig->OMDb_API->OMDb_API_KEY;

        $this->client->setParameterGet($data);
        $this->client->send();
        $response = $this->client->getResponse();
        
        if($response->isSuccess()) {
            return new JsonModel(json_decode($response->getBody(), true)["Search"]);
        } else {
            if ($this->env == 'development') { //temporary solution - using a fixture when movie API is unavailable
                $fixtureModelJson = "[{\"imdbID\": \"tt0123456\",
                                   \"Title\": \"The Red Hat and the Wolf\",
                                   \"Plot\": \"Once upon a time there lived a girl named Red Hat...\",
                                   \"Actors\": \"John Smith as Wolf, Jane Doe as Red Hat.\",
                                   \"rating\": 10,
                                   \"kinoRating\": 0,
                                   \"Year\": \"2015\"}]";
            
                return new JsonModel(json_decode($fixtureModelJson, true));
            }
        }
    }

    /**
     * Get movie moddel by id
     *
     * @param mixed $id
     * @return mixed
     */
    public function get ($id)
    {
        $data = array(
            "i" => $id,
            "apikey" => $this->omdbApiConfig->OMDb_API->OMDb_API_KEY
        );

        $this->client->setParameterGet($data);
        $this->client->send();
        $jsonString = $this->client->getResponse()->getBody();

        return new JsonModel(json_decode($jsonString, true));
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