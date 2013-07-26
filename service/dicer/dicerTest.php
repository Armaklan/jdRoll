<?php
include("dicer.php");
include("JetElt.php");
include("Dice.php");
include("Group.php");
include("StaticValue.php");

$dicer = new Dicer();
echo "<p> Resultat  (D6) : " . $dicer->parse("D6") . "</p>";
$dicer = new Dicer();
echo "<p> Resultat (3D8) : " . $dicer->parse("3D8") . "</p>";
$dicer = new Dicer();
echo "<p> Resultat (3D4E) : " . $dicer->parse("3D4E") . "</p>";
$dicer = new Dicer();
echo "<p> Resultat (3D4e) : " . $dicer->parse("3D4e") . "</p>";
$dicer = new Dicer();
echo "<p> Resultat (1D6E + 1D8) : " . $dicer->parse("1D6E + 1D8") . "</p>";
$dicer = new Dicer();
echo "<p> Resultat (1D6E + 1D8 + 3D6) : " . $dicer->parse("1D6E + 1D8 + 3D6") . "</p>";
$dicer = new Dicer();
echo "<p> Resultat (1D6E + 1D8 + 5) : " . $dicer->parse("1D6E + 1D8 + 5") . "</p>";

?>
