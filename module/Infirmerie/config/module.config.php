<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Infirmerie\Controller\Infirmerie' => 'Infirmerie\Controller\InfirmerieController'
				)
		),
		'router' => array (
				'routes' => array (
						'infirmerie' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/infirmerie[/][:action][/:idcons]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'idcons' => '[0-9]+' //fromRoute -ou- fromQuery -ou- dans le controller
										),
										'defaults' => array (
												'controller' => 'Infirmerie\Controller\Infirmerie',
												'action' => 'liste-patient'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/infirmerie' => __DIR__ .'/../view/layout/infirmerie.phtml',
						'layout/menugauche_infirm' => __DIR__ .'/../view/layout/menugauche_infirm.phtml',
						'layout/piedpage_infirm' => __DIR__ .'/../view/layout/piedpage_infirm.phtml'
				),
 				'template_path_stack' => array (
 						'infirmerie' => __DIR__ .'/../view'
 				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);