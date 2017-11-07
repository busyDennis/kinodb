<?php
namespace Kino\Model;

use Kino\Logger\CustomEclipseLogger;

use Zend\Db\TableGateway\TableGateway;


if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));

class CommentTable
{

    protected $tableGateway;

    public function __construct (TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll ()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getComment ($commentID)
    {
        $resultSet = $this->tableGateway->select(
                array(
                        'commentID' => $commentID
                ));
        
        if ($resultSet->count() == 0)
            return false;
        else {
            $row = $resultSet->current();
        
            if (! $row) {
                throw new \Exception("Could not find row $commentID");
            }
            return $row;
        }
    }

    public function getMovieComments ($imdbID)
    {
        $rowset = $this->tableGateway->select(
                array(
                        'imdbID' => $imdbID
                ));
        if (! $rowset) {
            throw new \Exception("Could not find movie $imdbID");
        }
        return $rowset;
    }

    public function saveComment (Comment $comment)
    {
        $data = array(
                'imdbID' => $comment->imdbID,
                'commentHeading' => $comment->commentHeading,
                'commentText' => $comment->commentText,
                'rating' => $comment->rating,
                'created' => $comment->created,
                'ip' => $_SERVER['REMOTE_ADDR']
        );

        if(array_key_exists('commentID', $data) && ! empty($data['commentID'])) {
            $sqlUpdateRetVal = $this->tableGateway->update($data,
                array(
                        'commentID' => $commentID
                    ));
            
            if ($sqlUpdateRetVal == 0)
                throw new \Exception('Aborting - comment with the provided commentID value was not found in the database');
        } else { // comment model has no commentID assigned - generate it and save the model
            $commentID = $data['commentID'] = uniqid();
            
            if($this->tableGateway->insert($data) == 0)
                throw new \Exception('Insert operation did not succeed.');
        }

        return $commentID;
    }

    public function deleteComment ($commentID)
    {
        $this->tableGateway->delete(
                array(
                        'commentID' => $commentID
                ));
    }
}
?>
