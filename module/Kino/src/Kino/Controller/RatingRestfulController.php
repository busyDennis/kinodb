<?php
namespace Kino\Controller;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class RatingRestfulController extends RestfulControllerTemplate
{

    protected $ratingTable;

    public function indexAction ()
    {
        return new JsonModel();
    }

    /**
     * Return list of resources
     *
     * @return mixed
     */
    public function getList ()
    {
        $rset = $this->getRatingTable()->fetchAll();
        $ratings = array();
        foreach ($rset as $entry) {
            $ratings[] = $entry;
        }
        return new JsonModel($ratings);
    }

    /**
     * Return rating for a single id
     *
     * @param mixed $id
     * @return mixed
     */
    public function get ($imdb_id)
    {
        if (! $this->getRatingTable()->entryExists($imdb_id))
            $response = array(
                    'imdbID' => $imdb_id,
                    'avgRating' => 0
            ); // ,
                   // 'total_rating'
                   // =>
                   // "0",
                   // 'times_rated'
                   // =>
                   // "0");
        else {
            $response = (array) $this->getRatingTable()->getRating($imdb_id);
            unset($response['totalRating']);
            unset($response['timesRated']);
        }

        // return $this->getResponse()->setContent(json_encode($response));

        return new JsonModel($response);
    }

    /**
     * Create a new resource
     *
     * @param mixed $data
     * @return mixed
     */
    public function create ($data)
    {
        var_dump($data);

        $rating = new Rating();
        $rating->exchangeArray($data); // json_decode(key($data), true));
        $this->getRatingTable()->updateRating($rating);

        $response = $this->getResponseWithHeader();
        return $response;
    }

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

    public function getRatingTable ()
    {
        if (! $this->ratingTable) {
            $sm = $this->getServiceLocator();
            $this->ratingTable = $sm->get('Kino\Model\RatingTable');
        }
        return $this->ratingTable;
    }
}
?>
