<?php
return array (
		
		'controllers' => array ( 
				'invokables' => array (
						'Laboratoire\Controller\Laboratoire' => 'Laboratoire\Controller\LaboratoireController',
						'Laboratoire\Controller\Biologiste'  => 'Laboratoire\Controller\BiologisteController',
						'Laboratoire\Controller\Technicien'  => 'Laboratoire\Controller\TechnicienController',
				)
		),
		
		
		
		'router' => array (
				'routes' => array (
						
						'laboratoire' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/laboratoire[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Laboratoire\Controller\Laboratoire',
												'action' => 'liste-patient'
										)
								)
						),
						
						'technicien' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/technicien[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Laboratoire\Controller\Technicien',
												'action' => 'liste-bilans'
										)
								)
						),
						
						'biologiste' => array (
								'type' => 'segment',
								'options' => array (
										'route' => '/biologiste[/][:action][/:id][/:val]',
										'constraints' => array (
												'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
												'val' => '[0-9]+'
										),
										'defaults' => array (
												'controller' => 'Laboratoire\Controller\Biologiste',
												'action' => 'liste-resultats-analyses'
										)
								)
						)
						
				)
		),
		
		
		
		'view_manager' => array (
				'template_map' => array (
						//'layout/laboratoire' => __DIR__ .'/../view/layout/laboratoire.phtml',
						//'layout/menugauche' => __DIR__ .'/../view/layout/menugauchelab.phtml',
						//'layout/piedpage' => __DIR__ .'/../view/layout/piedpage.phtml'
				),

				
				'template_path_stack' => array (
						'laboratoire' => __DIR__ .'/../view',
				),
				
				
				'strategies' => array(
						'ViewJsonStrategy',
				),
		)
		
		
		
);


