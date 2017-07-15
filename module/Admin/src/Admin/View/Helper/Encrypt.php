<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Encrypt extends AbstractHelper{
	/**
	 * Crypter le mot de passe
	 * @param unknown $donnees
	 */
	const PASSWORD_HASH = 'MY_PASSWORD_HASH_WHICH_SHOULD_BE_SOMETHING_SECURE';
	public function _encryptPassword($value) {
		for($i = 0; $i < 10; $i ++) {
			$value = md5 ( $value . self::PASSWORD_HASH );
		}
		return $value;
	}
}