<?php
/**
 * Description of Group
 *
 * @package Group
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

function resultDescComparator($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

function resultAscComparator($a, $b) {
    return -1 * resultDescComparator($a, $b);
}


class Group extends JetElt {

    var $elements;
    var $operateur;

    function __construct($operateur) {
        $this->elements = array();
        $this->operateur = $operateur;
    }

    public function launch() {
        foreach ($this->elements as $elt) {
            $elt->launch();
        }
    }

    public function addElt($elt) {
        $this->elements[count($this->elements)] = $elt;
    }

    public function addEltDetail($elt) {
		foreach($elt->getElt() as $element) {
        	$this->elements[count($this->elements)] = $element;
		}
    }

	public function getElt() {
		return $this->elements;
	}

    public function getResultat() {
        if($this->operateur == "-") {
            return $this->calculSoustractResult();
        } elseif (stripos($this->operateur, 'g') !== FALSE) {
            return $this->calculKeepGreatResult();
        } elseif (stripos($this->operateur, 'l') !== FALSE) {
            return $this->calculKeepLessResult();
        } elseif (stripos($this->operateur, '>') !== FALSE) {
            return $this->calculKeepIfGreaterResult();
        } elseif (stripos($this->operateur, '<') !== FALSE) {
            return $this->calculKeepIfLesserResult();
        } elseif ($this->operateur == 'q') {
            return $this->calculSubstractResult();
        } else {
            return $this->calculAddResult();
        }
    }

    public function calculSoustractResult() {
        $resultat = 0;
        $firstResult = true;
        foreach ($this->elements as $elt) {
            if (!$firstResult) {
                $resultat -= $elt->getResultat();
            } else {
                $resultat += $elt->getResultat();
            }
            $firstResult = false;
        }
        return $resultat;
    }

    public function calculAddResult() {
        $resultat = 0;
        foreach ($this->elements as $elt) {
            $resultat += $elt->getResultat();
        }
        return $resultat;
    }
    public function calculSubstractResult() {
        $resultat = 0;
        foreach ($this->elements as $elt) {
			if($resultat == 0) $resultat += $elt->getResultat();
			else $resultat -= $elt->getResultat();
        }
        return abs($resultat);
	}

    public function calculKeepIfGreaterResult() {
      $seuil = explode(">", $this->operateur)[1];
      $resultats = array();
      foreach ($this->elements as $elt) {
        $launch = $elt->getResultat();
        if($launch > $seuil) {
          $resultats[count($resultats)] = $launch;
        }
      }
      return count($resultats);
    }

    public function calculKeepIfLesserResult() {
      $seuil = explode("<", $this->operateur)[1];
      $resultats = array();
      foreach ($this->elements as $elt) {
        $launch = $elt->getResultat();
        if($launch < $seuil) {
          $resultats[count($resultats)] = $launch;
        }
      }
      return count($resultats);
    }

    public function calculKeepGreatResult() {

        $nbKeepDice = explode("g", $this->operateur)[1];

        $resultats = array();
        foreach ($this->elements as $elt) {
            $resultats[count($resultats)] = $elt->getResultat();
        }
        uasort($resultats, 'resultDescComparator');
        $resultats = array_slice($resultats, 0, $nbKeepDice);
        $resultat = 0;
        foreach($resultats as $res) {
            $resultat += $res;
        }
        return $resultat;
    }

    public function calculKeepLessResult() {

        $nbKeepDice = explode("l", $this->operateur)[1];

        $resultats = array();
        foreach ($this->elements as $elt) {
            $resultats[count($resultats)] = $elt->getResultat();
        }
        uasort($resultats, 'resultAscComparator');
        $resultats = array_slice($resultats, 0, $nbKeepDice);
        $resultat = 0;
        foreach($resultats as $res) {
            $resultat += $res;
        }
        return $resultat;
    }

    public function toString() {
        $result = "";
        $printOp = $this->operateur;
        $specificOp = "";
        if($printOp != "+" and $printOp != "-") {
            $specificOp = $printOp;
            $printOp = "+";
            $result = $result . "(";
        }
        foreach ($this->elements as $elt) {

            if ($result != "" and $result != "(") {
                $result = $result . $printOp . " ";
            }
            $result = $result . $elt->toString();
        }
        if($specificOp != "") {
            $result = $result . ")" . $specificOp;
        }
        return $result;
    }


}

?>
