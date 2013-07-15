<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'CommentsModule\Controller\CommentsRest' => 'CommentsModule\Controller\CommentsRestController',
        	'CommentsModule\Controller\MoviesRest' => 'CommentsModule\Controller\MoviesRestController',
        	'CommentsModule\Controller\RatingsRest' => 'CommentsModule\Controller\RatingsRestController',
        ),
    ),

    'router' => array(
        'routes' => array(

      'movies' => array(
        'type' => 'segment',
        'options' => array(
          'route'    => '/backend_movies[/[:id]]',
          'constraints' => array(
       					'id'     => 'tt[0-9]{7}',
        			),
        			'defaults' => array(
        				'__NAMESPACE__' => 'CommentsModule\Controller',
        				'controller' => 'CommentsModule\Controller\MoviesRest',
          ),
        ),
      ),

      'comments' => array(
        'type'    => 'segment',
        'options' => array(
          'route'    => '/backend_comments[/[:id]]',
          'constraints' => array(
            'id'     => 'tt[0-9]{7}',
          ),
          'defaults' => array(
            '__NAMESPACE__' => 'CommentsModule\Controller',
            'controller' => 'CommentsModule\Controller\CommentsRest',
          ),
        ),
      ),

      'ratings' => array(
        'type'    => 'segment',
        'options' => array(
          'route'    => '/backend_ratings[/[:id]]',
          'constraints' => array(
        				//'id'     => 'tt[0-9]{7}',
        			),
        			'defaults' => array(
        				'__NAMESPACE__' => 'CommentsModule\Controller',
        				'controller' => 'CommentsModule\Controller\RatingsRest',
        			),
        		),
        	),
        		
        ),
    ),

  'view_manager' => array(
    'template_path_stack' => array(
      __DIR__ . '/../view',
    ),
  	'strategies' => array(
  		'ViewJsonStrategy',
  	),
  )
);
?>
