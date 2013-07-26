<?php

/**
 * Description of Group
 *
 * @author zuberl
 */
class  StaticValue extends JetElt {

    var $resultat;

    function __construct($expression) {
        $this->resultat = (int) trim($expression);
    }

    public function launch() {
    }

    public function getResultat() {
        return $this->resultat;
    }

    public function toString() {
        $result = $this->resultat . " ";
        return $result;
    }


}

?>
