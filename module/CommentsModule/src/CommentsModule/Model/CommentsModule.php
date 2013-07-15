<?php
	namespace CommentsModule\Model;
	
	class CommentsModule
	{
	    public $commentID;
	    public $imdbID;
	    public $heading;
	    public $comment;
	    public $rating;
	    public $created;
	    public $ip;
	    
	
	    public function exchangeArray($data)
	    {
	    	
	    	$this->commentID     = (!empty($data['commentID'])) ? $data['commentID'] : null;
	    	$this->imdbID     = (!empty($data['imdbID'])) ? $data['imdbID'] : null;
	    	$this->heading     = (!empty($data['heading'])) ? $data['heading'] : null;
	    	$this->comment     = (!empty($data['comment'])) ? $data['comment'] : null;
	    	$this->rating     = (!empty($data['rating'])) ? $data['rating'] : null;
	    	$this->created     = (!empty($data['created'])) ? $data['created'] : null;
	    	$this->ip     = (!empty($data['ip'])) ? $data['ip'] : null;
		}
	}
?>