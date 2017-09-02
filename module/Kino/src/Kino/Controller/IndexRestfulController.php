<?php
namespace Kino\Controller;
use Zend\View\Model\ViewModel;

class IndexRestfulController extends RestfulControllerTemplate
{

    public function indexAction ()
    {
        $view = new ViewModel();
        $view->setTemplate('application/index/index');
        //$view->setTerminal(true);

        return $view;
    }
}
?>
