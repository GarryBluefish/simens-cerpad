<?php
namespace Personnel\Model;

class HopitalService {
	public  $id_hopital;
	Public  $id_service;

	public function exchangeArray($data) {
		$this->id_hopital = (! empty ( $data ['ID_HOPITAL'] )) ? $data ['ID_HOPITAL'] : null;
		$this->id_service = (! empty ( $data ['ID_SERVICE'] )) ? $data ['ID_SERVICE'] : null;
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}