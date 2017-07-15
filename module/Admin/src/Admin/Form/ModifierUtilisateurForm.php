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
class ModifierUtilisateurForm extends Form
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
				'name' => 'RoleSelect',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'RoleSelect',
				),
		));
		
		
		$this->add(array(
				'name' => 'nomUtilisateur',
				'type' => 'Text',
				'options' => array (
						'label' => 'Nom'
				),
				'attributes' => array(
						'id' => 'nomUtilisateur',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'prenomUtilisateur',
				'type' => 'Text',
				'options' => array (
						'label' => 'Prenom'
				),
				'attributes' => array(
						'id' => 'prenomUtilisateur',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'username',
				'type' => 'Text',
				'options' => array (
						'label' => 'Nom d\'utilisateur'
				),
				'attributes' => array(
						'id' => 'username',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'password',
				'type' => 'Password',
				'options' => array (
						'label' => 'Actuel mot de passe'
				),
				'attributes' => array(
						'placeholder' => 'Actuel',
						'id' => 'password',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'nouveaupassword',
				'type' => 'Password',
				'options' => array (
						'label' => 'Nouveau mot de passe'
				),
				'attributes' => array(
						'placeholder' => 'Nouveau',
						'id' => 'nouveaupassword',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'confirmerpassword',
				'type' => 'Password',
				'options' => array (
						'label' => 'Confirmer mot de passe'
				),
				'attributes' => array(
						'placeholder' => 'Confirmer',
						'id' => 'confirmerpassword',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'service',
				'type' => 'Select',
				'options' => array (
						'label' => 'Service'
				),
				'attributes' => array(
						'id' => 'service',
				),
		));
		
		$this->add(array(
				'name' => 'fonction',
				'type' => 'Text',
				'options' => array (
						'label' => 'Fonction'
				),
				'attributes' => array(
						'id' => 'fonction',
				),
		));
		
		$this->add(array(
				'name' => 'role',
				'type' => 'Zend\Form\Element\radio',
				'options' => array (
						'label' => 'Role',
						'value_options' => array(
								'admin' => 'Admin',
								'medecin' => iconv ( 'ISO-8859-1', 'UTF-8','Médecin') ,
								'surveillant' => 'Surveillant',
								'laborantin' => 'Laborantin',
								'infirmier' => 'Infirmier',
								'radiologie' => 'Radiologie',
								'anesthesie' => iconv ( 'ISO-8859-1', 'UTF-8','Anesthésie') ,
								'facturation' => 'Facturation',
								'etat_civil' => 'Etat civil',
								),
				),
				'attributes' => array(
						'id' => 'role',
						'required' => true,
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
