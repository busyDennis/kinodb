<?php
namespace Kino\Controller;
use Zend\View\Model\JsonModel;

class CommentRestfulController extends RestfulControllerTemplate
{

    protected $commentsTable;

    /**
     * Return all comments
     *
     * @return mixed
     */
    public function getList ()
    {
        $rset = $this->getCommentsTable()->fetchAll();
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
        $rset = $this->getCommentsTable()->getMovieComments($id);
        $comments = array();
        foreach ($rset as $entry) {
            $comments[] = $entry;
        }
        return new JsonModel($comments);
    }

    /**
     * Create a new resource
     *
     * @param mixed $data
     * @return mixed
     */
    public function create ($data)
    {
        $comment = new Movie();
        $comment->exchangeArray($data);
        $commentID = $this->getCommentsTable()->saveComment($comment);
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

    public function getCommentsTable ()
    {
        if (! $this->commentsTable) {
            $sm = $this->getServiceLocator();
            $this->commentsTable = $sm->get('Kino\Model\CommentTable');
        }
        return $this->commentsTable;
    }
}
?>
