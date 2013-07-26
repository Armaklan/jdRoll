<?php

/**
 * Description of Group
 *
 * @author zuberl
 */
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

    public function getResultat() {
        $resultat = 0;
        foreach ($this->elements as $elt) {
            if ($operateur = "+") {
                $resultat += $elt->getResultat();
            }
        }
        return $resultat;
    }

    public function toString() {
        $result = "";
        foreach ($this->elements as $elt) {
            if ($result != "") {
                $result = $result . $this->operateur . " ";
            }
            $result = $result . $elt->toString();
        }
        return $result;
    }


}

?>
