<?php
return array(
    'modules' => array(
        'Kino'
    ),
    
    'controllers' => array(
        'invokables' => array(
            'Kino\Controller\IndexRestful' => 'Kino\Controller\IndexRestfulController',
            'Kino\Controller\MovieRestful' => 'Kino\Controller\MovieRestfulController',
            'Kino\Controller\CommentRestful' => 'Kino\Controller\CommentRestfulController',
            'Kino\Controller\RatingRestful' => 'Kino\Controller\RatingRestfulController'
        )
    ),
    
    'router' => array(
        'routes' => array(
            'index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Kino\Controller\IndexRestful',
                        'action' => 'index'
                    )
                )
            ),
            'movie' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/movie[/[:imdbID]]',
                    'constraints' => array(
                        'id' => 'tt[0-9]{7}'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Kino\Controller',
                        'controller' => 'Kino\Controller\MovieRestful'
                    )
                )
            ),
            // 'may_terminate' => true
            'comment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/comment[/[:id]]',
                    'constraints' => array(
                        'id' => 'tt[0-9]{7}'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Kino\Controller',
                        'controller' => 'Kino\Controller\CommentRestful'
                    )
                )
            ),
            // 'may_terminate' => true
            'rating' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rating[/[:id]]',
                    'constraints' => array(
                        'id' => 'tt[0-9]{7}'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Kino\Controller',
                        'controller' => 'Kino\Controller\RatingRestful'
                    )
                )
            )
        )
    ),
    // 'may_terminate' => true
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'ViewJsonStrategy' => 'Zend\Mvc\Service\ViewJsonStrategyFactory',
        )
    ),
    
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            )
        )
    ),
    
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/kino/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'kino' => __DIR__ . '/../view',
            
            // __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        )
    )
);

?>
