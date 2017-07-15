<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Facturation\Controller\Facturation' => 'Facturation\Controller\FacturationController'
				)
		),
		'router' => array (
				'routes' => array (
						'facturation' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/facturation[/][:action][/:idfacturation]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'idfacturation' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Facturation\Controller\Facturation',
												'action' => 'admission'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						'layout/facturation' => __DIR__ .'/../view/layout/facturation.phtml',
						'layout/menugauche_fact' => __DIR__ .'/../view/layout/menugauche_fact.phtml',
						'layout/piedpage_fact' => __DIR__ .'/../view/layout/piedpage_fact.phtml'
				),
				'template_path_stack' => array (
						'facturation' => __DIR__ .'/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);