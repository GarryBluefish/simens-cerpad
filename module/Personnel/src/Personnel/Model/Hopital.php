<?php
namespace Personnel\Model;

class Hopital {
	public  $id_hopital;
	Public  $nom_hopital;

	public function exchangeArray($data) {
		$this->id_hopital = (! empty ( $data ['ID_HOPITAL'] )) ? $data ['ID_HOPITAL'] : null;
		$this->nom_hopital = (! empty ( $data ['NOM_HOPITAL'] )) ? $data ['NOM_HOPITAL'] : null;
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}