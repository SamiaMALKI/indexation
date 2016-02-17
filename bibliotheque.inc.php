<?php

//recuperer les keywords
function get_meta_keywords($file)
{
	$tab_metas = get_meta_tags($file);
	if (in_array('keywords', $tab_metas) && $tab_metas['keywords'] != null) return $tab_metas['keywords'];
	else return '';
	
}

//recuperer la description
function get_meta_description($file)
{
	$tab_metas = get_meta_tags($file);
	if (in_array('description', $tab_metas) && $tab_metas['description'] != null) return $tab_metas['description'];
	else return '';
	
}

//transfomer un fichier en chaine de caracteres 
function file2chaine($file)
{
	$tab_lignes = file($file);
	$chaine = implode ($tab_lignes , ' ');
	
	$chaine = preg_replace('/\s[\s]+/','-',$chaine);
	
	return $chaine;
}

//recuperer les mots cles 
function explodeBIS($separateurs , $chaine)
{
	$tab_mots = array();
	$tok = strtok($chaine , $separateurs);
	if(strlen($tok) > 2 && strlen(utf8_encode($tok)) > 2)  $tab_mots[] = $tok;

	while ($tok != false)
	{
		$tok=strtok($separateurs);
		if(strlen($tok) >2 && strlen(utf8_encode($tok)) > 2 )  $tab_mots[] = $tok;
	}
	
	return $tab_mots;
}

// afficher contenu du tableau
function print_tab ($tab)
{
	foreach ($tab as $indice => $mot )
	{
		echo"$indice = $mot <br>"; 
	}
}

// recuperer title on utilisant DOM
function get_title($file)
{
	$dom = new DOMDocument;
	$dom->loadHTML(file2chaine($file));
	$title = $dom->getElementsByTagName('title');

	return $title->item(0)->nodeValue;
}

// title on utilisant une ER
function get_title_With_ER($file){
	
	$modele = '/<title>(.*)<\/title>/i';
	
	// return type boolean
	preg_match($modele,file2chaine($file), $title);
	
	return $title[1];
}

// remplacer les cars html par leurs vraie présentation
function entitesHTML2Caracts($chaine_html_entites)
{	
	$table_caracts_html   = get_html_translation_table(HTML_ENTITIES);
	$tableau_html_caracts = array_flip ( $table_caracts_html );
	$chaine_html_caracts  = strtr ($chaine_html_entites, $tableau_html_caracts);

    return mb_convert_encoding($chaine_html_caracts, "iso-8859-1", 'UTF-8');
}

// body on utilisant DOM
function get_Body_textContent($file)
{
	$dom = new DOMDocument;
	$dom->loadHTML(file2chaine($file));
	$body = $dom->getElementsByTagName('body');

	return $body->item(0)->textContent;
}

// body on utilisant une ER
function get_Body($file){
	$chaine = file2chaine($file);
	$modele =  '/<body[^>]*>(.*)<\/body>/is';
	
	if( preg_match($modele, $chaine, $body)){
		
		return $body[1];
	}
	else return " ";
}

// suppression des scripts dans le body
function strip_scripts($chaine){
	$modele = '/<script[^>]*?>.*?<\/script>/is';
	$html = preg_replace($modele, ' ',$chaine);
	return $html;
}

//transsformer les occurences en poids 
function occ2poids($tab , $coff){
	foreach( $tab as $mot=>$occ){
		if(strlen(utf8_encode($mot)) > 2  && strlen($mot) > 2 )
			$tab[trim($mot)] = $occ * $coff;
	}
	return $tab;
}

//fusionner les mots cles du head et du body
function fusion_tabH_tabB($tab , $tab2, $mot_vides)
{
	if( count($tab) > count($tab2)){
		$tab_court=$tab2;
		$tab_long=$tab;
	}
	else
	{
		$tab_long=$tab2;
		$tab_court = $tab;
	}
	
	// on parcours le grand tableau et on complete l'autre
	// pour s'assurer de la validité des mots
	foreach( $tab_long as $mot=>$occ)
	{
		
		if(!array_key_exists(utf8_encode($mot), $mot_vides)){
			if(array_key_exists ($mot ,$tab_court))
			{
					$tab_court[$mot] += $occ;
			}
			else
				{
					$tab_court[$mot] = $occ;
			}
		}
	}
	
	return $tab_court;
}


//retourne l'encodage d'une chaien de caractère 

function detectEncodage($chaine)
{

    if (mb_detect_encoding($chaine, "iso-8859-1") != false) 
        return "iso-8859-1";
    else if (mb_detect_encoding($chaine, "iso-8859-15") != false) 
        return "iso-8859-15";
	else 
		return "UTF-8";
}


// charger les mots vides a partir de la bb
function get_mot_vides_bdd()
{
	global $conn;
	$mots_vides = array();
	$sql = "SELECT * FROM mots_vides";
	//echo $sql."<br>";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
				$mots_vides[] = utf8_encode($row['MOT_VIDE']);
			
		}
	} else {
	    echo "Error: " . $sql . "<br>" . $conn->error;
	}	
	
	return $mots_vides;	
}

// charger les mots vides a partir d'un fichier
function chargerDICO($file)
{
	$fp = fopen($file, 'r');
	
	while( ! feof($fp))
	{
		$tab_mot_vides[] = trim(fgets($fp, 4096));
	}
	
	fclose($fp);
	
	return $tab_mot_vides;
}

function explorerDir($path, $tab_mot_vide)
{
	$folder = opendir($path);
	while($entree = readdir($folder))
	{
		//On ignore les entrées
		if($entree != "." && $entree != "..")
		{
			// On vérifie si il s'agit d'un répertoire
			if(is_dir($path."/".$entree))
			{
				$sav_path = $path;
				// Construction du path jusqu'au nouveau répertoire
				$path .= "/".$entree;
				//echo "DOSSIER = ", $path, "<BR>";
				// On parcours le nouveau répertoire
				explorerDir($path  , $tab_mot_vide);
				$path = $sav_path;
			}
			else
			{
				//C'est un fichier html ou pas
				$path_source = $path."/".$entree;
				
				if(stripos($path_source, '.htm'))
				{
					//echo 'On appelle le module indexation <br>';
					//echo $path_source, '<br>';
					indexer($path_source, $tab_mot_vide);
					
				}
				//Si c'est un .html
				//On appelle la fonction d'indexation
				//Dans le module_indexation.php
				//Par un include
			}
		}
	}
	closedir($folder);
}

function indexer($path_source, $tab_mot_vide)
{
	//les separateurs pour decouper le texte en mots
	static $separateurs=" )(-_,:.'’»\;\"=«<>&}{][@!?^} /\\";
	
	//echo "_________________ indexation appel : ".$i." _________________<br><br>";
	
	//______________________ traitement head________________________
	$title = get_title_With_ER($path_source);
	$keywords = get_meta_keywords($path_source);
	$description = get_meta_description($path_source);
	$chaine_head = $keywords . " " . $description . " ".$title;

	// conversion entites html ascii
	$chaine_head = entitesHTML2Caracts($chaine_head);
	
	//minuscule
	$chaine_head=strtolower($chaine_head);
	
	//decoupage du texte en elements/mots
	$tab_mots_head = explodeBIS ($separateurs , $chaine_head);
	
	
	//calcul de la frequence des mots 
	//suppression des doublons
	$tab_mots_occurrences_head = array_count_values($tab_mots_head);
	
	// calcul du poids des mots
	$tab_mots_poids_head = occ2poids($tab_mots_occurrences_head, 2);

	//______________________ Fin traitement head ________________________


	//_________________________ traitement body _________________________
	
	$body = get_Body($path_source);

	// body sans balises scripts
	$body_sans_scripts = strip_scripts($body);

	// suppression des balises html
	$clean_body = strip_tags($body_sans_scripts);
	
	// conversion entites html ascii
	$chaine_body = entitesHTML2Caracts($clean_body);
	
	//minuscule
	$chaine=strtolower($chaine_body);
	
	//decoupage du texte en elements/mots
	$tab_mots_body = explodeBIS($separateurs , $chaine);

	//calcul de la frequence des mots 
	//suppression des doublons
	$tab_mots_occurrences_body = array_count_values($tab_mots_body);
	
	// calcul du poids des mots
	$tab_mots_poids_body = occ2poids($tab_mots_occurrences_body, 1);
	
	//_________________________ fin traitement body ________________________

	// fusion des deux tableaux
	$tab_mots_poids = fusion_tabH_tabB($tab_mots_poids_head, $tab_mots_poids_body, $tab_mot_vide);
	
	
	// stockage dans la bdd
	
	$title = (strlen($title) <= 0) ? " No title" : $title;
	$description = ( strlen($description) <= 0) ? " No description" : $description; 
	
	save_indexed_document($tab_mots_poids,	$path_source, $title, $description);
	
	//print_tab($tab_mots_poids);
	echo"<br><br>________________________ another doc ____________________________<br><br>";
	
}

// sauvegarder le document indexer dans la bdd
function save_indexed_document($tab_mots_poids,	$path, $title, $description )
{
	global $conn;
	$title_cleaned =  mysqli_real_escape_string($conn, utf8_encode($title));
	$desc_cleaned =  mysqli_real_escape_string($conn, utf8_encode($description));

	$doc_id = 0;
	$conn->query("set names utf8");
	$sql = "INSERT INTO document values (' ','".$path."','".$title_cleaned."','".$desc_cleaned."')";

	if ($conn->query($sql) === TRUE) 
	{	
		// recuperer le dernier id creer 
		$sql = "select MAX(DOC_ID) last_id from document";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$doc_id = $row['last_id'];
		} 
	}
	else
	{
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	
	// inserer les mots cles associer*/
	if($doc_id > 0)
	{
		foreach( $tab_mots_poids as $mot=>$poids)
		{
			inserer_mots_document($doc_id, $mot, $poids);
		}
	}	
}

// sauvegarder la liste des mots cles d'un document
function inserer_mots_document($doc_id, $mot, $poids)
{
	
	$mot = utf8_encode($mot);
	global $conn;
	$mot_id = 0;
	$conn->query("set names utf8");
	
	// verifier que le mot n'existe pas 
	$sql_select = "select MOT_ID from mot where TRIM(MOT_MOT) ='".$mot."')";
	$result = $conn->query($sql_select);
	
	if($result && $result->num_rows)
	{
		$row = $result->fetch_assoc();
		$mot_id = $row['MOT_ID'];
	} 
	
	if($mot_id <= 0)
	{
		$conn->query("set names utf8");
		// insertion du mot
		$sql = "INSERT INTO mot values (' ','".$mot."')";
		if ($conn->query($sql) === TRUE) 
		{	
			$conn->query("set names utf8");
			// recuperer le dernier id creer 
			$sql = "select MAX(MOT_ID) last_id from mot";
			$result = $conn->query($sql);
			
			if($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$mot_id = $row['last_id'];
			} 
		}
		else
		{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
	
	if($mot_id > 0)
	{
		$conn->query("set names utf8");
		$sql = "INSERT INTO doc_mot values ('".$doc_id."','".$mot_id."','".$poids."')";
		$conn->query($sql);
	}
}
?>