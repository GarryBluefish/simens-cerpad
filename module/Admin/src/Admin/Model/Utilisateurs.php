<?php
namespace Admin\Model;

class Utilisateurs
{
	public $id;
	public $username;
	public $password;
	public $role;
	public $fonction;
	public $idpersonne;
	

	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->username = (!empty($data['username'])) ? $data['username'] : null;
		$this->password  = (!empty($data['password'])) ? $data['password'] : null;
		$this->role  = (!empty($data['role'])) ? $data['role'] : null;
		$this->fonction  = (!empty($data['fonction'])) ? $data['fonction'] : null;
		$this->idpersonne  = (!empty($data['idpersonne'])) ? $data['idpersonne'] : null;
	}
}