<?php
namespace Kino\Controller;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RestfulControllerTemplate extends AbstractRestfulController
{

    public function indexAction ()
    {
        return $this->routeNotImplemented();
    }

    public function getList ()
    {
        return $this->routeNotImplemented();
    }

    public function get ($id)
    {
        return $this->routeNotImplemented();
    }

    public function create ($data)
    {
        return $this->routeNotImplemented();
    }

    public function update ($id, $data)
    {
        return $this->routeNotImplemented();
    }

    public function delete ($id)
    {
        return $this->routeNotImplemented();
    }

    function routeNotImplemented ()
    {
        $this->response->setStatusCode(405);
        throw new \Exception('This method is not implemented.');

        // return new JsonModel(
        // array(
        // 'Information:' => 'This route is not implemented.'
        // ));
    }
}

?>
