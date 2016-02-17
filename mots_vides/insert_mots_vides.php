<?php
include "../bdd/connect.php";
include "../bibliotheque.inc.php";


$file_mot_vide = "mot_vide.txt";

$tab_mots_vides = chargerDICO($file_mot_vide);
$conn->query("set names utf8");

foreach( $tab_mots_vides as $key=>$value){
	global $conn;
	
	$sql = "INSERT INTO mots_vides values ('".$value."')";
	echo $sql;
	if ($conn->query($sql) === TRUE) {
	    echo "New record ".$value." created successfully <br>";
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}	
}

$conn->close();

?>
