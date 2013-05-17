<?php

class StatutCampagn {

	public static $OPEN = 0;
	public static $PAUSE = 1;
	public static $CLOSE = 2;

	public function listStatut() {
		$result = array();
		$result[0] = "Ouverte";
		$result[1] = "En Pause";
		$result[2] = "Fermer";
		return $result;
	}
}

?>