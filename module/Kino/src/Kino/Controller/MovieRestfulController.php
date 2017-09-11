<?php
namespace Kino\Controller;
use \Zend\Config\Config;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Response;
use \Zend\Log\Logger;
use Zend\View\Model\JsonModel;

class MovieRestfulController extends RestfulControllerTemplate
{

    protected $commentsTable;

    protected $ratingsTable;

    private $logger;

    private $client;

    public function __construct ()
    {
        // ad hoc Eclipse logger setup
        $outputFileHandle = fopen("zend_log.txt", "w+");
        $this->logger = new Logger();
        $this->logger->addWriter('stream', null,
                array(
                        'stream' => $outputFileHandle
                ));

        // config loaded
        $this->customConfig = new Config(include 'config/customConfig.php');

        // test:
        $this->logger->debug($this->customConfig->OMDb_API->URL_ROOT);

        // HTTP client setup
        $this->client = new Client($this->customConfig->OMDb_API->URL_ROOT,
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
                    "s" => "Star Trek",
                    "apikey" => $this->customConfig->OMDb_API->API_KEY
            );
        } else
            $data["apikey"] = $this->customConfig->OMDb_API->API_KEY;

        $this->client->setParameterGet($data);
        $this->client->send();
        $jsonString = $this->client->getResponse()->getBody();

        return new JsonModel(json_decode($jsonString, true)["Search"]);
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
                "apikey" => $this->customConfig->OMDb_API->API_KEY
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
