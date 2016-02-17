<?php
include "../bdd/connect.php";
include "../bibliotheque.inc.php";

deleteAll();


$conn->close();

function deleteAll(){
	global $conn;
	
	$sql = "DELETE FROM mots_vides";
	//echo $sql;
	if ($conn->query($sql) === TRUE) {
	    echo "All records deleted successfully <br>";
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}	
}


?>
