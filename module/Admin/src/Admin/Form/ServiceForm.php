<?php
/**
 * File for Login Form Class
 *
 * @category  User
 * @package   User_Form
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */

/**
 * @namespace
 */
namespace Admin\Form;

/**
 * @uses Zend\Form\Form
 */
use Zend\Form\Form;

/**
 * Login Form Class
 *
 * Login Form
 *
 * @category  User
 * @package   User_Form
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */
class ServiceForm extends Form
{
    /**
     * Initialize Form
     */
	
	public function __construct($name = null)
	{
		// we want to ignore the name passed
		parent::__construct();
	
		$this->add(array(
				'name' => 'id',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'id',
				),
		));
		
		$this->add(array(
				'name' => 'nom',
				'type' => 'Text',
				'options' => array (
						'label' => 'Nom du service'
				),
				'attributes' => array(
						'id' => 'nom',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'domaine',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' =>  iconv ( 'ISO-8859-1', 'UTF-8','Domaine'),
						'value_options' => array (
								'' => '',
								'Logistique' => iconv ( 'ISO-8859-1', 'UTF-8', 'Logistique' ),
								'MÃ©decine' => iconv ( 'ISO-8859-1', 'UTF-8', 'Médecine' ),
								'Administration' => iconv ( 'ISO-8859-1', 'UTF-8', 'Administration' )
						)
				),
				'attributes' => array(
						'id' => 'domaine',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'tarif',
				'type' => 'Text',
				'options' => array (
						'label' => 'Tarif'
				),
				'attributes' => array(
						'id' => 'tarif',
						'required' => true,
				),
		));
		
		
		/*Buttons --- Buttons --- Buttons --- Buttons*/
		$this->add(array(
				'name' => 'terminer',
				'type' => 'button',
				'attributes' => array(
						'value' => 'Terminer',
						'id' => 'terminer',
				),
		));
		
		$this->add(array(
				'name' => 'enregistrer',
				'type' => 'button',
				'attributes' => array(
						'value' => 'Enregistrer',
						'id' => 'enregistrer',
				),
		));
		
		$this->add(array(
				'name' => 'annuler',
				'type' => 'button',
				'attributes' => array(
						'value' => 'Annuler',
						'id' => 'annuler',
				),
		));
		
	}
}
