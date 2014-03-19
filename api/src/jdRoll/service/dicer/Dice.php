<?php
/**
 * Description of Dice
 *
 * @package Dice
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */
class Dice extends JetElt {

    var $type;
    var $resultat;
    var $explosif = false;
    var $limitedExplosif = true;

    function __construct($expression) {

        $lastCar = substr(trim($expression), -1);
        if ( $lastCar == "E" or $lastCar == "S") {
            $this->explosif = true;
            $this->limitedExplosif = false;
            $expression = substr($expression, 0, -1);
        } elseif ($lastCar == "e" or $lastCar == "s") {
            $this->explosif = true;
            $this->limitedExplosif = true;
            $expression = substr($expression, 0, -1);
        }


        $this->type = (int) $expression;
    }

    public function launch() {
        $this->resultat = (int) $this->launchOne();
        while(true) {
            if($this->explosif && ($this->resultat == $this->type)) {
                $this->resultat = $this->resultat + (int) $this->launchOne();
            } elseif ( (!$this->limitedExplosif) && (($this->resultat % $this->type) == 0)) {
                $this->resultat = $this->resultat + (int) $this->launchOne();
            } else {
                break;
            }
        }
    }

    private function launchOne() {
        return rand(1, $this->type);
    }

    public function toString() {
        $result = "d" . $this->type;
        if($this->explosif && $this->limitedExplosif) {
            $result = $result . "e";
        } elseif($this->explosif) {
            $result = $result . "E";
        }
        $result = $result . " ( " . $this->resultat . " ) ";
        return $result;
    }


    public function getResultat() {
        return $this->resultat;
    }
}

?>
