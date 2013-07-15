<?php
namespace CommentsModule\Model;

use Zend\Db\TableGateway\TableGateway;

class RatingsModuleTable
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

    public function getRating($imdb_id) {
        $rowset = $this->tableGateway->select(array('imdb_id' => $imdb_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $imdb_id");
        }
        return $row;
    }
	
    public function entryExists($imdb_id) {
    	if($this->tableGateway->select(array('imdb_id' => $imdb_id))->current())
    		return true;
    	else
    		return false;
    }
    
    public function saveRating($rating) {
    	
    }
    
    public function updateRating(RatingsModule $rating)
    {
    	$imdb_id = $rating->imdb_id;
    	$entry = $this->tableGateway->select(array('imdb_id' => $imdb_id))->current();
    	 
    	$data = array();
    	$data['imdb_id'] = $rating->imdb_id;
    	if ($entry) {
    		$data['total_rating'] = $rating->total_rating + $entry->total_rating;
    		$data['times_rated'] = $entry->times_rated + 1;
    		$data['avg_rating'] = round( ( ( float )$data['total_rating'] ) / $data['times_rated'], 1);  //1 is the precision
    		$this->tableGateway->update($data, array('imdb_id' => $imdb_id));
    	} else {
    		$data['total_rating'] = $rating->total_rating;
    		$data['times_rated'] = '1';
    		$data['avg_rating'] = $rating->total_rating;
    		$this->tableGateway->insert($data);
    	}
    }

    
    /*public function updateRating(RatingsModule $rating)
    {
        $data = array(
            'imdb_id' => $rating->imdb_id,
            'avg_rating' => $rating->avg_rating,
        	'total_rating' => $rating->total_rating,
        	'times_rated' => $rating->times_rated,
        );
        
        $imdb_id = $rating->imdb_id;

        $containsEntry = $this->tableGateway->select(array('imdb_id' => $imdb_id))->current();
        if ($containsEntry) {
        	$this->tableGateway->update($data, array('imdb_id' => $imdb_id));
        } else {
        	$this->tableGateway->insert($data);
        }
    }*/
}
?>
