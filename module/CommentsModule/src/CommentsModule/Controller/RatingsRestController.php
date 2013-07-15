<?php
	namespace CommentsModule\Controller;
	
	use CommentsModule\Model\RatingsModule;
	use Zend\Mvc\Controller\AbstractRestfulController;
	use Zend\View\Model\JsonModel;
	use Zend\View\Model\ViewModel;
	
	class RatingsRestController extends AbstractRestfulController {
		protected $ratingsTable;
		
	    public function indexAction() {
	        return new JsonModel();
	    }
	 
	    /**
	     * Return list of resources
	     *
	     * @return mixed
	     */
	    public function getList() {
	    	$rset = $this->getRatingsTable()->fetchAll();
	    	$ratings = array();
	    	foreach($rset as $entry){
	    		$ratings[] = $entry;
	    	}
	    	return new JsonModel($ratings);
	    	//return new JsonModel();
	    }
	 
	    /**
	     * Return rating for a single id
	     *
	     * @param  mixed $id
	     * @return mixed
	     */
	    public function get($imdb_id) {
	    	if(! $this->getRatingsTable()->entryExists($imdb_id))
	    		$response = array('imdb_id' => $imdb_id, 'avg_rating' => "0");  //, 'total_rating' => "0", 'times_rated' => "0");
	    	else {
	    		$response = (array)$this->getRatingsTable()->getRating($imdb_id);
	    		unset($response['total_rating']);
	    		unset($response['times_rated']);
	    	}
	    	return $this->getResponse()->setContent(json_encode($response));
	    }
	     
	 
	    /**
	     * Create a new resource
	     *
	     * @param  mixed $data
	     * @return mixed
	     */
	    public function create($data) {
	    	var_dump($data);
	    	
	    	$rating = new RatingsModule();
	    	$rating->exchangeArray($data);//json_decode(key($data), true));
	    	$this->getRatingsTable()->updateRating($rating);
	    	 
	    	$response = $this->getResponseWithHeader();
	    	return $response;
	    	
	    }
	 
	    /**
	     * Update an existing resource
	     *
	     * @param  mixed $id
	     * @param  mixed $data
	     * @return mixed
	     */
	    public function update($id, $data) {
	    	/*echo "id: ";
	    	var_dump($id);
	    	echo "data: ";
	    	var_dump($data);
	    	echo "key: ".key($data);*/
	    	
	    	/*$rating = new RatingsModule();
	    	$rating->exchangeArray(json_decode(key($data), true));
	    	
	    	$this->getRatingsTable()->updateRating($rating);
	    	 
	    	$response = $this->getResponseWithHeader();
	    	return $response;*/
	    }
	 
	    /**
	     * Delete an existing resource
	     *
	     * @param  mixed $id
	     * @return mixed
	     */
	    public function delete($id) {}
	    
	    public function getResponseWithHeader()
	    {
	    	$response = $this->getResponse();
	    	$response->getHeaders()
	    	//make can accessed by *
	    	->addHeaderLine('Access-Control-Allow-Origin','*')
	    	//set allow methods
	    	->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET');
	    
	    	return $response;
	    }
	    
	    public function getRatingsTable() {
	    	if (!$this->ratingsTable) {
	    		$sm = $this->getServiceLocator();
	    		$this->ratingsTable = $sm->get('CommentsModule\Model\RatingsModuleTable');
	    	}
	    	return $this->ratingsTable;
	    }
	}
?>
