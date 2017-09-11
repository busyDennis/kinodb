<?php
namespace Kino\Model;
use Zend\Db\TableGateway\TableGateway;

class RatingTable
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

    public function getRating ($imdbID)
    {
        $rowset = $this->tableGateway->select(
                array(
                        'imdbID' => $imdbID
                ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $imdbID");
        }
        return $row;
    }

    public function entryExists ($imdbID)
    {
        if ($this->tableGateway->select(
                array(
                        'imdbID' => $imdbID
                ))->current())
            return true;
        else
            return false;
    }

    public function saveRating ($rating)
    {}

    public function updateRating (Ratings $rating)
    {
        $imdbID = $rating->imdbID;
        $entry = $this->tableGateway->select(
                array(
                        'imdbID' => $imdbID
                ))->current();

        $data = array();
        $data['imdbID'] = $rating->imdbID;
        if ($entry) {
            $data['totalRating'] = $rating->totalRating + $entry->totalRating;
            $data['timesRated'] = $entry->timesRated + 1;
            $data['avgRating'] = round(
                    ((float) $data['totalRating']) / $data['timesRated'], 1); // 1
                                                                                // is
                                                                                // the
                                                                                // precision
            $this->tableGateway->update($data,
                    array(
                            'imdbID' => $imdbID
                    ));
        } else {
            $data['totalRating'] = $rating->totalRating;
            $data['timesRated'] = 1;
            $data['avgRating'] = $rating->totalRating;
            $this->tableGateway->insert($data);
        }
    }

    /*
     * public function updateRating(RatingsModule $rating)
     * {
     * $data = array(
     * 'imdb_id' => $rating->imdb_id,
     * 'avg_rating' => $rating->avg_rating,
     * 'total_rating' => $rating->total_rating,
     * 'times_rated' => $rating->times_rated,
     * );
     *
     * $imdb_id = $rating->imdb_id;
     *
     * $containsEntry = $this->tableGateway->select(array('imdb_id' =>
     * $imdb_id))->current();
     * if ($containsEntry) {
     * $this->tableGateway->update($data, array('imdb_id' => $imdb_id));
     * } else {
     * $this->tableGateway->insert($data);
     * }
     * }
     */
}
?>
