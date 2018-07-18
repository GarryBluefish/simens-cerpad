<?php

namespace Secretariat\Model;

use Secretariat\View\Helper\DateHelper;

class listeDossierPatient{
	
	public $numero_dossier;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $sexe;
	public $adresse;
	public $nationalite_actuelle;
	public $option;
	public $idpersonne;
	
	protected $typepatient;
	protected $iddemande;
	
	public function exchangeArray($data) {
		
		$this->numero_dossier = (! empty ( $data ['numero_dossier'] )) ? $data ['numero_dossier'] : null;
		$this->nom = (! empty ( $data ['nom'] )) ?        "<div id='nomMaj' style='max-width: 130px; overflow: hidden;' >".$data ['nom']."</div>" : null;
		$this->prenom = (! empty ( $data ['prenom'] )) ?        "<div style='max-width: 160px; overflow: hidden;' >".$data ['prenom']."</div>" : null;
		$this->date_naissance = (! empty ( $data ['date_naissance'] )) ? (new DateHelper())->convertDate($data ['date_naissance']) : null;
		$this->sexe = (! empty ( $data ['sexe'] )) ? $data ['sexe'] : null;
		$this->adresse = (! empty ( $data ['adresse'] )) ?        "<div class='adresseText' style='max-width: 230px; overflow: hidden;' >".$data ['adresse']."</div>" : null;    
		$this->nationalite_actuelle = (! empty ( $data ['nationalite_actuelle'] )) ?        "<div>".$data ['nationalite_actuelle']."</div>" : null;
		
		
		if(array_key_exists('typepatient', $data)){
			if($data ['typepatient'] == 1){
				$typePatient ="<span style='display: none;'> patient_depister </span> <span style='display: none;'> patient_interne </span>";
			}else{
				$typePatient ="<span style='display: none;'> patient_depister </span> <span style='display: none;'> patient_externe </span>";
			}
		}else{
			$typePatient ="<span style='display: none;'> patient_externe </span>";
		}
		
		if(array_key_exists('iddemande', $data)){
			$annulerPatient = "";
		}else{
			$annulerPatient = "<a id='".$data ['idpersonne']."' href='javascript:envoyer(".$data ['idpersonne'].")'><img src='../images_icons/symbol_supprimer.png' title='Supprimer' > </a>";
		}
		
		$this->option = (! empty ( $data ['idpersonne'] )) ? 
		                "<infoBulleVue> <a href='javascript:visualiser(".$data ['idpersonne'].")' ><img style='margin-right: 10%;' src='../images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue><infoBulleVue> <a href='javascript:modifierPatient(".$data ['idpersonne'].")' ><img style='display: inline; margin-right: 15%;' src='../images_icons/pencil_16.png' title='Modifier'></a> </infoBulleVue>".$typePatient.$annulerPatient : null;
		
		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? $data ['idpersonne'] : null;
 		
	}
	
	public function getArrayCopy() {
		return array_values(get_object_vars ( $this ));
	}
	
}