<?php
namespace Kino;
use Kino\Model\Kino;
use Kino\Model\CommentTable;
use Kino\Model\Rating;
use Kino\Model\RatingTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Zend\View\Model\JsonModel;
use Zend\I18n\Translator\Translator;

class Module
{

    // public function onBootstrap(MvcEvent $e)
    // {
    // // Register a "render" event, at high priority (so it executes prior
    // // to the view attempting to render)
    // $app = $e->getApplication();
    // $app->getEventManager()->attach('render', array($this,
    // 'registerJsonStrategy'), 100);
    // }

    public function onBootstrap (MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR,
                array(
                        $this,
                        'onDispatchError'
                ), 0);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR,
                array(
                        $this,
                        'onRenderError'
                ), 0);
    }

    public function onDispatchError ($e)
    {
        return $this->getJsonModelError($e);
    }

    public function onRenderError ($e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError ($e)
    {
        $error = $e->getError();
        if (! $error) {
            return;
        }

        $response = $e->getResponse();
        $exception = $e->getParam('exception');
        $exceptionJson = array();
        if ($exception) {
            $exceptionJson = array(
                    'class' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'message' => $exception->getMessage(),
                    'stacktrace' => $exception->getTraceAsString()
            );
        }

        $errorJson = array(
                'message' => 'An error occurred during execution; please try again later.',
                'error' => $error,
                'exception' => $exceptionJson
        );
        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }

        $model = new JsonModel(
                array(
                        'errors' => array(
                                $errorJson
                        )
                ));

        $e->setResult($model);

        return $model;
    }

    public function registerJsonStrategy (MvcEvent $e)
    {
        $app = $e->getTarget();
        $locator = $app->getServiceManager();
        $view = $locator->get('Zend\View\View');
        // $translator = null;
        // $pluginManager = $view->getHelperPluginManager();
        // $helper =
        // $pluginManager->get('Zend\I18n\View\Helper\AbstractTranslatorHelper');

        $jsonStrategy = $locator->get('ViewJsonStrategy');
        // $view->addRenderingStrategy($jsonStrategy);
    }

    public function getAutoloaderConfig ()
    {
        return array(
                'Zend\Loader\StandardAutoloader' => array(
                        'namespaces' => array(
                                __NAMESPACE__ => __DIR__ . '/src/' .
                                         __NAMESPACE__
                        )
                )
        );
    }

    public function getConfig ()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig ()
    {
        return array(
                'factories' => array(
                        'Kino\Model\CommentTable' => function  ($sm)
                        {
                            $tableGateway = $sm->get('CommentTableGateway');
                            $table = new CommentTable($tableGateway);
                            return $table;
                        },

                        'CommentTableGateway' => function  ($sm)
                        {
                            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                            $resultSetPrototype = new ResultSet();
                            $resultSetPrototype->setArrayObjectPrototype(
                                    new Kino());
                            return new TableGateway('comment', $dbAdapter, null,
                                    $resultSetPrototype);
                        },

                        'Kino\Model\RatingTable' => function  ($sm)
                        {
                            $tableGateway = $sm->get('RatingTableGateway');
                            $table = new RatingTable($tableGateway);
                            return $table;
                        },

                        'RatingTableGateway' => function  ($sm)
                        {
                            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                            $resultSetPrototype = new ResultSet();
                            $resultSetPrototype->setArrayObjectPrototype(
                                    new Rating());
                            return new TableGateway('rating', $dbAdapter, null,
                                    $resultSetPrototype);
                        }
                )
        );
    }
}
