<?php
namespace Kino\Model;

class Rating
{

    public $imdbID;
    public $avgRating;
    public $totalRating;
    public $timesRated;

    public function exchangeArray ($data)
    {
        $this->imdbID = (! empty($data['imdbID'])) ? $data['imdbID'] : null;
        $this->avgRating = (! empty($data['avgRating'])) ? $data['avgRating'] : null;
        $this->totalRating = (! empty($data['totalRating'])) ? $data['totalRating'] : null;
        $this->timesRated = (! empty($data['timesRated'])) ? $data['timesRated'] : null;
    }
}
?>