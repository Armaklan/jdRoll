<?php
/**
 * Description of dicer
 *
 * @package dicer
 * @copyright (C) 2013 jdRoll
 * @license MIT
 */

class Dicer {
    var $elements = array();

    public function parse($expression) {
		$expression = str_replace(' ', '', $expression);
        $this->elements = array();
        $this->parseGroup($expression);
        $this->launch();
        return $this->toString();
    }

    private function parseGroup($expression) {
		$regularExpr = "/\((.*)\)[lg][0-9]/";
		$tabOccur = array();
		if (preg_match($regularExpr, $expression, $tabOccur)) {
				$occur = $tabOccur[0];
				$expression = str_replace($occur, "", $expression);
				if(preg_match("/^\+/", $expression)) {
					$expression = substr($expression, -1);
				}
				$occurElt = explode(")", str_replace("(", "", $occur));

				$dicePart = $occurElt[0];
				$operateur = $occurElt[1];

				$groupElt = explode("+", $occurElt[0]);
				$group = new Group($operateur);
				foreach($groupElt as $elt) {
					$result = $this->parseGroupElt($elt);
					$group->addEltDetail($result);
				}
				$this->elements[count($this->elements)] = $group;
		}
		if($expression != "") {
			$groupElt = explode("+", $expression);
			if(count($groupElt) == 1) {
				$group = $this->parseGroupElt($expression);
				$this->elements[count($this->elements)] = $group;
			} else {
				$group = new Group("+");
				foreach($groupElt as $elt) {
					$result = $this->parseGroupElt($elt);
					$group->addElt($result);
				}
				$this->elements[count($this->elements)] = $group;
			}
		}
    }

    private function parseGroupElt($expression) {
        $groupElt = explode("-", $expression);
        if(count($groupElt) == 1) {
            return $this->parseDicesExpression($expression);
        } else {
            $group = new Group("-");
            foreach($groupElt as $elt) {
                $result = $this->parseDicesExpression($elt);
                $group->addElt($result);
            }
            return $group;
        }
    }

    private function parseDicesExpression($expression) {
        $expression = str_replace('D','d', $expression);
        if ( stripos($expression, 'd') === FALSE ) {
            return new StaticValue($expression);
        } else {
            $group = null;
            if ( stripos($expression, 'g') !== FALSE ) {
                $exprElt = explode("g", $expression);
                $nbKeepDice = $exprElt[1];
                $expression = $exprElt[0];
                $group = new Group("g$nbKeepDice");
            } elseif (stripos($expression, 'l') !== FALSE) {
                $exprElt = explode("l", $expression);
                $nbKeepDice = $exprElt[1];
                $expression = $exprElt[0];
                $group = new Group("l$nbKeepDice");
            } else {
                $group = new Group("+");
            }
            $results = array();
            $nombre = 0;
            $diceDetail = "";

            $diceElt = explode("d", $expression);

            if($diceElt[0] == "") {
                $nombre = 1;
            } else {
                $nombre = (int) $diceElt[0];
            }

            $diceDetail = $diceElt[1];

            for ($i = 0; $i < $nombre; $i++) {
                if($i > 0 and ( stripos($diceDetail, 's') !== FALSE or stripos($diceDetail, 'S') !== FALSE)) {
                    $diceDetail = str_replace('s', '', $diceDetail);
                    $diceDetail = str_replace('S', '', $diceDetail);
                }
                $group->addElt(new Dice($diceDetail));
            }
            return $group;
        }
    }

    private function launch() {
        foreach ($this->elements as $elt) {
            $elt->launch();
        }
    }

    private function toString() {
        $result = "";
		$resultat = 0;
        foreach ($this->elements as $elt) {
			if($result != "") $result .= " + ";
            $result = $result . $elt->toString();
			$resultat += $elt->getResultat();
        }
        $result = $result . " = " . $resultat;
        return $result;
    }
}

?>
