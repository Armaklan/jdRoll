<?php
include("dicer.php");
include("JetElt.php");
include("Dice.php");
include("Group.php");
include("StaticValue.php");

$dicer = new Dicer();
echo "<p> Resultat  (D6) : " . $dicer->parse("d6") . "</p>";
echo "<p> Resultat (3D8) : " . $dicer->parse("3d8") . "</p>";
echo "<p> Resultat (3D4E) : " . $dicer->parse("3d4E") . "</p>";
echo "<p> Resultat (3D4e) : " . $dicer->parse("3d4e") . "</p>";
echo "<p> Resultat (1D6E + 1D8) : " . $dicer->parse("1d6E + 1d8") . "</p>";
echo "<p> Resultat (1D6E + 1D8 + 3D6) : " . $dicer->parse("1d6E + 1d8 + 3d6") . "</p>";
echo "<p> Resultat (1D6E + 1D8 + 5) : " . $dicer->parse("1d6E + 1d8 + 5") . "</p>";
echo "<p> Resultat (1D6E - 1D8 + 5) : " . $dicer->parse("1d6E - 1d8 + 5") . "</p>";
echo "<p> Resultat (1D6E - 1D8 + 5) : " . $dicer->parse("1d6E - 1d8 + 5") . "</p>";
echo "<p> Resultat (1D6E - 1D8 - 5) : " . $dicer->parse("1d6E - 1d8 - 5") . "</p>";
echo "<p> Resultat (3d6g2) : " . $dicer->parse("3d6g2") . "</p>";
echo "<p> Resultat (3d6Eg2) : " . $dicer->parse("3d6Eg2") . "</p>";
echo "<p> Resultat (3d6l2) : " . $dicer->parse("3d6l2") . "</p>";
echo "<p> Resultat (3d6s) : " . $dicer->parse("3d6s") . "</p>";
echo "<p> Resultat (3d6S) : " . $dicer->parse("3d6S") . "</p>";

?>
