<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">
<title>Moteur de recherche</title>
<script src="js/script.js"></script>
<script src="js/jquery.min.js" ></script>
</head>
<body>
 <link rel="stylesheet" href="css_menu/style.css">
<?php
include "bdd/connect.php";
$total_pages;
$current_page = 1;
$per_page = 3;
?>

 <section class="container_menu">
    <menu>
      <ul class="menu">
       
        <li><a href="index.html" >Accueil</a></li>
        <li><a href="rechercher.php" >Recherche</a></li>
        <li><a href="indexation.php" >Indexation</a></li>
        <li><a href="motVide.php" >Mot Vide</a></li>
		  <li><a href="corpus.php" >Corpus</a></li>
      </ul>
    </menu>
  </section>
 <div id="myContent">
 
	<div id="formulaire3">
		<form action = "lire_corpus/lecture_corpus.php" method="get">
			<div class="input-container">
			<input class="btn1" name="search" type="submit" value="corpus"/>
			</div>
			
		</form>
		
	</div>
	
</div>
<div id="content">
	<?php

	if(isset($_GET["mot"]) && isset($_GET["search"]))
	{
		$mot = $_GET["mot"];
		
		$sql_count = "SELECT count(*) count FROM mot m, doc_mot dm, document d WHERE m.MOT_MOT='" . utf8_decode($mot) . "' and m.MOT_ID=dm.MOT_ID and dm.DOC_ID=d.DOC_ID order by(dm.POIDS) DESC";
		
		if(!isset($total_pages)){
			$result = $conn->query($sql_count);
			if($result){
				$row = $result->fetch_assoc();
				echo "<p id=\"resultcount\">Nombre de résultats pour $mot : <b>".$row['count']."</b></p>";
				$total_pages =($row['count']/$per_page);
				if($row['count'] % $per_page != 0) $total_pages++;
			}
		}
		
		$current_page = (isset($_GET["page"]) && $_GET["page"] >= 1 && $_GET["page"] <= $total_pages ) ? $_GET["page"] : 1;
		$start = ($current_page - 1)* $per_page;
		
		
		$sql = "SELECT d.DOC_ID, d.DOC_ADDR DOC_TITRE, d.DOC_TITRE  DOC_ADDR, d.DOC_DESC, dm.POIDS POIDS FROM mot m, doc_mot dm, document d WHERE m.MOT_MOT='" . utf8_decode($mot) . "' and m.MOT_ID=dm.MOT_ID and dm.DOC_ID=d.DOC_ID order by(dm.POIDS) DESC limit $start,$per_page";
		
		$result = $conn->query($sql);
		
		if($result && $result->num_rows)
		{	
			// id du mot
			while($row = $result->fetch_assoc())
			{
				
				$id_doc = $row["DOC_ID"];
				
				$poids = $row["POIDS"]; 
			
				$titre = utf8_encode($row["DOC_TITRE"]);
				
				$adresse = utf8_encode($row["DOC_ADDR"]);
				
				$description = utf8_encode($row["DOC_DESC"]);
				
				echo "<p class=\"boite\"><b><u><a class=\"titre\" href=\"".$adresse."\">".$titre. "</a></u></b><br>";
				echo "<span class=\"link\">".$adresse. " Poids : $poids</span><br>";
				echo $description. "<br>";
				echo "<a class=\"myLink\" onclick=\"showTag($id_doc)\">Consulter Nuage</a>";
				echo "</p>";
				echo "<div class=\"cloud\" id=\"tag_$id_doc\"></div>";
			}?>
			<div id="footer">Page : 
			
			<?php
				for($i = 1; $i <= $total_pages; $i++){
					$link = "rechercher.php?mot=$mot&search=chercher&page=$i";
					echo "<a href=\"$link\">$i</a>"." ";	
				}
			?>
			</div>
			<?php
		}
		else
		{
				echo "<p id=\"resultcount\">Pas de résultat pour : <b>$mot</b></p>";
		}
	}
	?>
</div>

</body>
</html>