<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Secretariat\Controller\Secretariat' => 'Secretariat\Controller\SecretariatController'
				)
		),
		'router' => array (
				'routes' => array (
						'secretariat' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/secretariat[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Secretariat\Controller\Secretariat',
												'action' => 'liste-patient'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/secretariat' => __DIR__ .'/../view/layout/secretariat.phtml',
						'layout/menugauche' => __DIR__ .'/../view/layout/menugauche.phtml',
						'layout/piedpage' => __DIR__ .'/../view/layout/piedpage.phtml'
				),
				'template_path_stack' => array (
						'secretariat' => __DIR__ .'/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);