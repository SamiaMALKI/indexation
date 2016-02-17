<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Nuages des mots-clès</title>
</head>
<body>
<?php
include "bdd/connect.php";
include "bdd/r.php";


$list_documents = selectDocuments();

 ?>
 <div id="container">
	<h1 id="header" align=center>Liste des documents</h1>
	<table id="liste">
		<tr>
			<th>Titre </th>
			<th>Lien</th>
			<th>Nuage des mots-clès</th>
		</tr>
		<?php
		foreach($list_documents as $row)
		{
		?>
		<tr>
			<td><?php echo $row['DOC_TITRE']; ?> </td>
			<td><a href="<?php echo $row['DOC_ADDR']; ?>">Visiter le site</a></td>
			<td><a href="generate_tags_cloud.php?doc_id=<?php echo $row['DOC_ID']; ?>">Consulter</a></td>
		</tr>
		<?php
		}
		?>
	</table>
 </div>
</body>
</html>