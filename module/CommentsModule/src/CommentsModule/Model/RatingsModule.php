<?php
	namespace CommentsModule\Model;
	
	class RatingsModule
	{
	    public $imdb_id;
	    public $avg_rating;
	    public $total_rating;
	    public $times_rated;
	
	    public function exchangeArray($data)
	    {
	    	$this->imdb_id     = (!empty($data['imdb_id'])) ? $data['imdb_id'] : null;
	    	$this->avg_rating     = (!empty($data['avg_rating'])) ? $data['avg_rating'] : null;
	    	$this->total_rating     = (!empty($data['total_rating'])) ? $data['total_rating'] : null;
	    	$this->times_rated     = (!empty($data['times_rated'])) ? $data['times_rated'] : null;
		}
	}
?>