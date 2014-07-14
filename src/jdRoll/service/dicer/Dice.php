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
    var $arsStyle;
    var $explosif = false;
    var $limitedExplosif = true;

    function __construct($expression) {

        $lastCar = substr(trim($expression), -1);
        if ( $lastCar == "E" or $lastCar == "S") {
            $this->explosif = true;
            $this->limitedExplosif = false;
            $this->arsStyle = false;
            $expression = substr($expression, 0, -1);
        } elseif ($lastCar == "e" or $lastCar == "s") {
            $this->explosif = true;
            $this->limitedExplosif = true;
            $this->arsStyle = false;
            $expression = substr($expression, 0, -1);
        } else if($lastCar == "A" or $lastCar == "a") {
            $this->explosif = false;
            $this->limitedExplosif = false;
            $this->arsStyle = true;
        }

        $this->type = (int) $expression;
    }


    public function launch() {
        if($this->arsStyle) {
            $this->launchArs();
        } else {
            $this->launchStandard();
        }
    }

    public function launchStandard() {
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

    public function launchArs() {
        $this->resultat = 0;
        $lastLaunch = 0;
        $multiplicateur = 1;
        while(true) {
            $lastLaunch = ((int) $this->launchOne()) - 1;
            if ( $lastLaunch == 1) {
                $multiplicateur = $multiplicateur * 2;
            } else {
                $this->resultat = $lastLaunch * $multiplicateur;
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
        } elseif($this->arsStyle) {
            $result = $result . "A";
        }
        $result = $result . " ( " . $this->resultat . " ) ";
        return $result;
    }


    public function getResultat() {
        return $this->resultat;
    }
}

?>
