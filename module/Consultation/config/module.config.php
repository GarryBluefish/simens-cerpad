<?php
return array (
		'controllers' => array (
				'invokables' => array (
						'Consultation\Controller\Consultation' => 'Consultation\Controller\ConsultationController'
				)
		),
		'router' => array (
				'routes' => array (
						'consultation' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/consultation[/][:action][/:idconsultation]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'idconsultation' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Consultation\Controller\Consultation',
												'action' => 'liste-consultations'
										)
								)
						)
				)
		),
		'view_manager' => array (
				'template_map' => array (
						//'layout/consultation' => __DIR__ .'/../view/layout/consultation.phtml',
						//'layout/menugauche_fact' => __DIR__ .'/../view/layout/menugauche_fact.phtml',
						//'layout/piedpage_fact' => __DIR__ .'/../view/layout/piedpage_fact.phtml'
				),
				'template_path_stack' => array (
						'consultation' => __DIR__ .'/../view'
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
);