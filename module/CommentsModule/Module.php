<?php
namespace CommentsModule;

use CommentsModule\Model\CommentsModule;
use CommentsModule\Model\CommentsModuleTable;
use CommentsModule\Model\RatingsModule;
use CommentsModule\Model\RatingsModuleTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;


class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
    	return array(
    		'factories' => array(
    			'CommentsModule\Model\CommentsModuleTable' =>  function($sm) {
    				$tableGateway = $sm->get('CommentsModuleTableGateway');
    				$table = new CommentsModuleTable($tableGateway);
    				return $table;
    			},
    			'CommentsModuleTableGateway' => function ($sm) {
    				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    				$resultSetPrototype = new ResultSet();
    				$resultSetPrototype->setArrayObjectPrototype(new CommentsModule());
    				return new TableGateway('comments', $dbAdapter, null, $resultSetPrototype);
    			},
    			
    			'CommentsModule\Model\RatingsModuleTable' =>  function($sm) {
    				$tableGateway = $sm->get('RatingsModuleTableGateway');
    				$table = new RatingsModuleTable($tableGateway);
    				return $table;
    			},
    			'RatingsModuleTableGateway' => function ($sm) {
    				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    				$resultSetPrototype = new ResultSet();
    				$resultSetPrototype->setArrayObjectPrototype(new RatingsModule());
    				return new TableGateway('ratings', $dbAdapter, null, $resultSetPrototype);
    			},
    			
    		),
    	);
    }
    
    
}
