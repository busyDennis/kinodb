<?php
	namespace CommentsModule\Controller;
	
	use Zend\Mvc\Controller\AbstractRestfulController;
	use Zend\View\Model\ViewModel;
	use Zend\View\Model\JsonModel;
	use Zend\Http\Request;
	use Zend\Http\Client;
	use Zend\Http\Response;
	use Zend\Stdlib\ParametersInterface;
	
	
	class MoviesRestController extends AbstractRestfulController
	{
		protected $commentsTable;
		protected $ratingsTable;
		
	    public function indexAction()
	    {
	        return new JsonModel();
	    }
	 
	    /**
	     * Return list of resources
	     *
	     * @return mixed
	     */
	    public function getList()
	    {
	    	$data = (array)$this->getRequest()->getQuery();	//return array
	    	if(!array_key_exists('q', $data) && !array_key_exists('id', $data)){
	    		$data = array("q" => "Star Trek", "limit" => "10");
	    	}
	    	$data['type'] = "json";
	    	
	    	$temp = http_build_query($data);
	    	$curl = curl_init('http://mymovieapi.com/?'.$temp);
	    	curl_setopt_array($curl, array(
	    			CURLOPT_RETURNTRANSFER => 1
	    	));
	    	
	    	$result = curl_exec($curl);
	    	curl_close($curl);
	    	
	    	return new JsonModel(json_decode($result));
	    }
	 
	    /**
	     * Return single resource
	     *
	     * @param  mixed $id
	     * @return mixed
	     */
	    public function get($id)
	    {
	    	$data['type'] = "json";
	    	
	    	$curl = curl_init('http://mymovieapi.com/?id='.$id);
	    	curl_setopt_array($curl, array(
	    			CURLOPT_RETURNTRANSFER => 1
	    	));
	    	
	    	$result = curl_exec($curl);
	    	curl_close($curl);
	    	
	    	return $this->getResponse()->setContent($result);
	    }
	     
	 
	    /**
	     * Create a new resource
	     *
	     * @param  mixed $data
	     * @return mixed
	     */
	    public function create($data) {}
	 
	    /**
	     * Update an existing resource
	     *
	     * @param  mixed $id
	     * @param  mixed $data
	     * @return mixed
	     */
	    public function update($id, $data) {}
	 
	    /**
	     * Delete an existing resource
	     *
	     * @param  mixed $id
	     * @return mixed
	     */
	    public function delete($id) {}
	    
	    public function getCommentsTable()
	    {
	    	if (!$this->commentsTable) {
	    		$sm = $this->getServiceLocator();
	    		$this->commentsTable = $sm->get('CommentsModule\Model\CommentsModuleTable');
	    	}
	    	return $this->commentsTable;
	    }
	    
	}
?>
