<?php
namespace Kino\Controller;
use Kino\Model\Comment;
use Zend\View\Model\JsonModel;

use \Zend\Log\Logger;



if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'w'));

if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));




class CommentRestfulController extends RestfulControllerTemplate
{

    protected $commentTable;

    
    
    public function __construct() {
        // ad hoc Eclipse IDE logger setup
        $this->logger = new Logger();
        
        $logDir = $_SERVER['DOCUMENT_ROOT'].'/..'.'/log/';
        // check if the log dir exists
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logWriteStream = fopen($logDir."custom.log", "w+", false);
        $this->logger->addWriter('stream', null,
            array(
                'stream' => $logWriteStream
            )
            );
        
        
    }
    
    
    /**
     * Return all comments
     *
     * @return mixed
     */
    public function getList ()
    {
        $rset = $this->getCommentTable()->fetchAll();
        $movies = array();
        foreach ($rset as $entry) {
            $movies[] = $entry;
        }
        return new JsonModel($movies);
    }

    /**
     * Return all comments for a single movie
     *
     * @param mixed $id
     * @return mixed
     */
    public function get ($id)
    {
        $rset = $this->getCommentTable()->getMovieComments($id);
        $comments = array();
        foreach ($rset as $entry) {            
            $comments[] = $entry;
        }
        return new JsonModel($comments);
    }

    /**
     * Create a new resource
     *
     * @param mixed $data - ignored
     * @return mixed
     */
    public function create ($data)
    {   
        $json_arr = json_decode(file_get_contents("php://input"), true, 512, JSON_UNESCAPED_UNICODE)[0];
        

        $this->logger->debug("I am a cool little sentence hanging out here on my own");
        $this->logger->debug($json_arr);        
        
        
        $comment = new Comment();
        $comment->exchangeArray($json_arr);
        $commentID = $this->getCommentTable()->saveComment($comment);
        $response = $this->getResponseWithHeader();
        $response->setContent(
                json_encode(
                        array(
                                'commentID' => $commentID
                        )));
        $response->getHeaders()->addHeaders(
                array(
                        'Content-Type' => 'application/json'
                ));

        return $response;
    }

    // configure response
    public function getResponseWithHeader ()
    {
        $response = $this->getResponse();
        $response->getHeaders()
            ->
        // make can accessed by *
        addHeaderLine('Access-Control-Allow-Origin', '*')
            ->
        // set allow methods
        addHeaderLine('Access-Control-Allow-Methods', 'POST PUT DELETE GET');

        return $response;
    }

    public function getCommentTable ()
    {
        if (! $this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('Kino\Model\CommentTable');
        }
        return $this->commentTable;
    }
}
?>
