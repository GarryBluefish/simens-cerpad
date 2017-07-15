<?php
return array(
		
		'controllers' => array(
				'invokables' => array(
						'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
				),
		),
			
		// The following section is new and should be added to your file
				'router' => array(
						'routes' => array(
								//Pour gerer la connexion a partir de --- localhost/simens/public
								'home' => array(
										'type' => 'Zend\Mvc\Router\Http\Literal',
										'options' => array(
												'route'    => '/',
												'defaults' => array(
														'controller' => 'Admin\Controller\Admin',
														'action'     => 'login',
												),
										),
								),
								
								'admin' => array(
										'type'    => 'Segment',
										'options' => array(
												'route'    => '/admin[/][:action][/:id]',
												'constraints' => array(
														'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
														'id'     => '[0-9]+',
												),
												'defaults' => array(
														'controller' => 'Admin\Controller\Admin',
														'action'     => 'login',
												),
										),
										
								),
						),
				),
		
		/**
		 * ================================================================
		 * ================================================================
		 * ================================================================
		 */
		
		'di' => array(
				'instance' => array(
						'Admin\Event\Authentication' => array(
								'parameters' => array(
										'userAuthenticationPlugin' => 'Admin\Controller\Plugin\UserAuthentication',
										'aclClass'                 => 'Admin\Acl\Acl'
								)
						),
						'Admin\Acl\Acl' => array(
								'parameters' => array(
										'config' => include __DIR__ . '/acl.config.php'
								)
						),
		
		
						'Admin\Controller\Plugin\UserAuthentication' => array(
								'parameters' => array(
										'authAdapter' => 'Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter'
								)
						),
							
						'Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter' => array(
								'parameters' => array(
										'tableName' => 'utilisateurs',
										'identityColumn' => 'username',
										'credentialColumn' => 'password',
								)
						),
				),
		),
		
		/**
		 * ================================================================
		 * ================================================================
		 * ================================================================
		 */
		
		
		'view_manager' => array(
				'template_path_stack' => array(
						'admin' => __DIR__ . '/../view',
				),
				'strategies' => array(
						'ViewJsonStrategy',
				),
		
		),
);