<?php
namespace Kino\Model;

class Comment
{

    public $commentID;

    public $imdbID;

    public $commentHeading;

    public $commentText;

    public $rating;

    public $created;

    public $ip;

    public function exchangeArray ($data)
    {
        $this->commentID = (! empty($data['commentID'])) ? $data['commentID'] : null;
        $this->imdbID = (! empty($data['imdbID'])) ? $data['imdbID'] : null;
        $this->commentHeading = (! empty($data['commentHeading'])) ? $data['commentHeading'] : null;
        $this->commentText = (! empty($data['commentText'])) ? $data['commentText'] : null;
        $this->rating = (! empty($data['rating'])) ? $data['rating'] : null;
        $this->created = (! empty($data['created'])) ? $data['created'] : null;
        $this->ip = (! empty($data['ip'])) ? $data['ip'] : null;
    }
}
?>