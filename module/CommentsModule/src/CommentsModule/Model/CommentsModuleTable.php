<?php
namespace CommentsModule\Model;

use Zend\Db\TableGateway\TableGateway;

class CommentsModuleTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getComment($commentID)
    {
    	$commentID = (int) $commentID;
        $rowset = $this->tableGateway->select(array('commentID' => $commentID));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
    public function getMovieComments($imdbID)
    {
    	$rowset = $this->tableGateway->select(array('imdbID' => $imdbID));
    	if (!$rowset) {
    		throw new \Exception("Could not find movie $imdbID");
    	}
    	return $rowset;
    }

    public function saveComment(CommentsModule $comment)
    {
        $data = array(
            'imdbID' => $comment->imdbID,
            'heading' => $comment->heading,
        	'comment' => $comment->comment,
       		'rating' => $comment->rating,
       		'created' => $comment->created,
       		'ip' => $_SERVER['REMOTE_ADDR'],
        );

        $commentID = (int) $comment->commentID;
        if ($commentID == 0) {
            $this->tableGateway->insert($data);
            $commentID = $this->tableGateway->getAdapter()->getDriver()->getLastGeneratedValue();
        } else {
            if ($this->getComment($commentID)) {
                 $this->tableGateway->update($data, array('commentID' => $commentID));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        return $commentID;
    }

    public function deleteComment($commentID)
    {
        $this->tableGateway->delete(array('commentID' => $commentID));
    }
}
?>
