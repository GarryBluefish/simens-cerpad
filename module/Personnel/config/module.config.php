<?php
return array(
		'controllers' => array(
				'invokables' => array(
						'Personnel\Controller\Personnel' => 'Personnel\Controller\PersonnelController',
				),
		),
		'router' => array(
				'routes' => array(
						'personnel' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/personnel[/][:action][/:id][/:val]',
										'constraints' => array(
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id'     => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array(
												'controller' => 'Personnel\Controller\Personnel',
												'action'     => 'index',
										),
								),
						),
				),
		),
		'view_manager' => array(
				'template_map' => array (
						'layout/personnel' => __DIR__ . '/../view/layout/personnel.phtml',
						'layout/menugauche-personnel' => __DIR__ . '/../view/layout/menugauche-personnel.phtml',
						'layout/piedpage-personnel' => __DIR__ . '/../view/layout/piedpage-personnel.phtml'
				),
				'template_path_stack' => array(
						'personnel' => __DIR__ . '/../view',
				),
		),
);