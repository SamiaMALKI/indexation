<?php
include "../bdd/connect.php";
include "../bibliotheque.inc.php";

readAll();


$conn->close();

function readAll(){
	
	$result = get_mot_vides_bdd();
	foreach($result as $mot=>$content){
		echo $mot ." : ". $content;
	}
}



?>
