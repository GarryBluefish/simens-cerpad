<?php
namespace Facturation\Model;

class Service {
	public  $id_service;
	Public  $nom;
	public  $domaine;

	public function exchangeArray($data) {
		$this->id_service = (! empty ( $data ['ID_SERVICE'] )) ? $data ['ID_SERVICE'] : null;
		$this->nom = (! empty ( $data ['NOM'] )) ? $data ['NOM'] : null;
		$this->domaine = (! empty ( $data ['DOMAINE'] )) ? $data ['DOMAINE'] : null;
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}