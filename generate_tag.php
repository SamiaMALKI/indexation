
<?php

include "bdd/connect.php";

$somme=0;
$color = array();

$color[] = "#B0CC99";
$color[] = "#677E52";
$color[] = "#B7CA79";
$color[] = "#F6E8B1";
$color[] = "#89725B";
$color[] = "#C79F4B";
$color[] = "#A67E2E";
$color[] = "#663E10";
$color[] = "#570906";
$color[] = "#3B0405";


if(isset($_GET['doc_id']))
{	
	global  $conn;
	$sql = "select m.MOT_MOT  mot, d.POIDS poids from doc_mot d, mot m where d.DOC_ID =".$_GET['doc_id']." and d.MOT_ID = m.MOT_ID ";
	$list= array();
	$result = $conn->query($sql);
	if ($result && $result->num_rows) {
		while($row = $result->fetch_assoc()) {
			$list[] = $row;
			
			$somme =+  $row['poids'];
		}
	}

	$conn->close();
}


if(count($list))
{
	$counter = 0;
	foreach( $list as $row){
		$k =  ($row['poids'] / $somme);
		$i= ($k * 32 )/ 10;
		echo "<span><a href=\"rechercher.php?mot=".utf8_encode($row['mot'])."\" style=\"color:".$color[$counter].";font-size:".$i."px\">".utf8_encode($row['mot'])."</a></span>";
		$counter = ($counter < 9) ?  ($counter + 1) : 0;
	}
}

?>