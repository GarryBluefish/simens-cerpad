<?php
namespace Consultation\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DateHelper extends AbstractHelper{

	/**
	 * Trouver une date future
	 * @param $date = date actuelle
	 * @param $jour = nombre de jour
	 * @return string
	 */
	function getDateProchaine($date,$jour){ // la date doit avoir cette format 'Y-m-d'
		list($y,$m,$d)= explode("-",$date);
		$date = mktime(0,0,0,$m,$d+$jour,$y);
		$date = gmdate("Y-m-d", $date);
		return $date;
	}
	
	public function convertDate($date){
		$nouv_date = substr($date, 8, 2).'/'.substr($date, 5, 2).'/'.substr($date, 0, 4);
		return $nouv_date;
	}

	public function convertDateInAnglais($date){
		$nouv_date = substr($date, 6, 4).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2);
		return $nouv_date;
	}
	
	public function convertDateTime ($dateTime) {
		$date = substr($dateTime, 8, 2).'/'.substr($dateTime, 5, 2).'/'.substr($dateTime, 0, 4);
		$time = substr($dateTime, 11, 8);
		return $date." - ".$time;
	}
	
	public function convertDateTimeHm ($dateTime) {
		$date = substr($dateTime, 8, 2).'/'.substr($dateTime, 5, 2).'/'.substr($dateTime, 0, 4);
		$time = substr($dateTime, 11, 5);
		return $date." - ".$time;
	}
	
	public function convertTime ($dateTime) {
		$time = substr($dateTime, 11, 8);
		return $time;
	}
	
	public function convertDateFormatyyyymmdd ($date) {
		$nouv_date = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);
		return $nouv_date;
	}
	
	public function decouperTimeHm ($time) {
		$time = substr($time, 0, 5);
		return $time;
	}
	
	public function getTime ($dateTime) {
		$time = substr($dateTime, 11, 8);
		return $time;
	}
	
	/*
	 * La date est au format : dd/MM/yyyy
	*/
	public function jourEnLettre($date_a){
		$b = explode('/',$date_a);
		$c = $b[1].'/'.$b[0].'/'.$b[2];
		$time = strtotime($c);
		$date = date("D",$time);
		//$date = date("D d F Y � H:i:s",$time); //On r�cup�re le jour, le mois, l'ann�e
		$date = str_replace("Mon", "Lundi", $date);
		$date = str_replace("Tue", "Mardi", $date);
		$date = str_replace("Wed", "Mercredi", $date);
		$date = str_replace("Thu", "Jeudi", $date);
		$date = str_replace("Fri", "Vendredi", $date);
		$date = str_replace("Sat", "Samedi", $date);
		$date = str_replace("Sun", "Dimanche", $date);
		$date = str_replace("January", "Janvier", $date);
		$date = str_replace("February", "F�vrier", $date);
		$date = str_replace("March", "Mars", $date);
		$date = str_replace("April", "Avril", $date);
		$date = str_replace("May", "Mai", $date);
		$date = str_replace("June", "Juin", $date);
		$date = str_replace("July", "Juillet", $date);
		$date = str_replace("August", "A�ut", $date);
		$date = str_replace("September", "Septembre", $date);
		$date = str_replace("October", "Octobre", $date);
		$date = str_replace("November", "Novembre", $date);
		$date = str_replace("December", "D�cembre", $date);

		return $date;
	}

	public function moisEnLettre($date_a){ //La date est au format : dd/MM/yyyy
		$b = explode('/',$date_a);
		$c = $b[1].'/'.$b[0].'/'.$b[2];
		$time = strtotime($c);
		$date = date("F",$time);
		//$date = date("D d F Y � H:i:s",$time); //On r�cup�re le jour, le mois, l'ann�e
		$date = str_replace("Mon", "Lundi", $date);
		$date = str_replace("Tue", "Mardi", $date);
		$date = str_replace("Wed", "Mercredi", $date);
		$date = str_replace("Thu", "Jeudi", $date);
		$date = str_replace("Fri", "Vendredi", $date);
		$date = str_replace("Sat", "Samedi", $date);
		$date = str_replace("Sun", "Dimanche", $date);
		$date = str_replace("January", "Janvier", $date);
		$date = str_replace("February", "F�vrier", $date);
		$date = str_replace("March", "Mars", $date);
		$date = str_replace("April", "Avril", $date);
		$date = str_replace("May", "Mai", $date);
		$date = str_replace("June", "Juin", $date);
		$date = str_replace("July", "Juillet", $date);
		$date = str_replace("August", "A�ut", $date);
		$date = str_replace("September", "Septembre", $date);
		$date = str_replace("October", "Octobre", $date);
		$date = str_replace("November", "Novembre", $date);
		$date = str_replace("December", "D�cembre", $date);

		return $date;
	}

	public function lePremierDimancheDuMois(){
		$today = new \DateTime();
		$leMoisActuel = $today->format('m'); //le mois actuel
		$AnneeActuelle =  $today->format('Y'); //l'ann�e actuelle

		$lePremierJourDuMois = $this->jourEnLettre('01/'.$leMoisActuel.'/'.$AnneeActuelle);//Le premier jour du mois

		$laSemaine = array(
				'Lundi'    => '7',
				'Mardi'    => '6',
				'Mercredi' => '5',
				'Jeudi'    => '4',
				'Vendredi' => '3',
				'Samedi'   => '2',
				'Dimanche' => '1',
		);

		return $lePremierDimanche = $laSemaine[$lePremierJourDuMois];
	}

	public function lePremierDimancheDuMoisByDate($leMois,$Annee){

		$lePremierJourDuMois = $this->jourEnLettre('01/'.$leMois.'/'.$Annee);//Le premier jour du mois

		$laSemaine = array(
				'Lundi'    => '7',
				'Mardi'    => '6',
				'Mercredi' => '5',
				'Jeudi'    => '4',
				'Vendredi' => '3',
				'Samedi'   => '2',
				'Dimanche' => '1',
		);

		return $lePremierDimanche = $laSemaine[$lePremierJourDuMois];
	}

	/************************************************/
	//$mois= mktime(0,0,0,2,1,2014);
	//$date = $NombreDeJourDuMois = date("t",$mois);
	/****************************************************************************************************************************/

	/**========================================================================================================================**/

	/****************************************************************************************************************************/
	public function dateNormEnChiffre($date){
		$nouv_date = substr($date, 0, 4).substr($date, 5, 2).substr($date, 8, 2);
		return $nouv_date;
	}

	public function dateChiffreEnNorm($date){
		$nouv_date = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);
		return $nouv_date;
	}


	
	/**************************************************/
	/*******************************************************************************************************************/
	/** CLONAGE **/
	/* MY CLONE  */
	/*=================================================================================================================*/
	
	/*******************************************************************************************************************/
	
	public function construisTableauJS($tableauPHP){
		$nomTableauJS = array();
		echo $nomTableauJS." = new Array();";
		foreach ( $tableauPHP as $i => $val) {
			if(!is_array($tableauPHP[$i]))	echo $nomTableauJS."['".$i."'] = '".addslashes($tableauPHP[$i])."';";
			else
				$this->construisTableauJS($tableauPHP[$i], $nomTableauJS."[".$i."]");
		}
		return $nomTableauJS;
	}
}