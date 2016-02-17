<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body>
<?php
include "bdd/connect.php";
include "bibliotheque.inc.php";


//$file_mot_vide = "mot_vide.txt";


$tab_mots_vides = get_mot_vides_bdd();
$mots_vides = array_flip($tab_mots_vides);
//print_tab($mots_vides);

?>
<P>
<B>DEBUTTTTTT DU PROCESSUS :</B>
<BR>
<?php echo " ", date ("h:i:s"); ?>
</P>
<?php

//Augmentation du temps 20 min
//d'exécution de ce script
set_time_limit (1200);
$path= "./lire_corpus/ccm";

//$conn->query("set names utf8");
explorerDir($path, $mots_vides);


?>
<P>
<B>FINNNNNN DU PROCESSUS :</B>
<BR>
<?php echo " ", date ("h:i:s"); ?>
</P>
</body>
</html>