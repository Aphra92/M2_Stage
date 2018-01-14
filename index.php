<?php
	session_start();
	//Include
	$title="Welcome";
	include("includes/start.php");
	include("includes/dblog.php");
?>
<div id = "contindex">
<div class="index">
	<!-- Ajout de données --> 
	<?php
	if (isset($_SESSION['id']) && $_SESSION['id'] > 0)
		echo 
		'<div class="conteneur select_newdata droite">
			<a href="./select_newdata.php">
				<h2 class ="head"> New Data </h2>
				<p>Add data to the database</p>
				<img src="./images/upload.png" class = "image_index_1" alt="upload" >
			</a>
		</div>';
	?>

	<!-- Téléchargement --> 
	<?php
	if (!isset($_SESSION['id']))
		echo
		'<div class="conteneur DLCL_1 droite">
			<a href="./dlcl.php">
				<h2 class ="head"> Download </h2>
				<p>Download repertoire at CL format </p>
				<img src="./images/download.png" class = "image_index_1" alt="download">
			</a>
		</div>';
	else 
		echo 
		'<div class="conteneur DLCL_1 mid">
			<a href="./dlcl.php">
				<h2 class ="head"> Download </h2>
				<p>Download repertoire at CL format </p>
				<img src="./images/download.png" class = "image_index_1" alt="download">
			</a>
		</div>';
	?>

	<!-- Curation --> 
	<?php
	if (isset($_SESSION['id']) && $_SESSION['id'] > 1)
		echo 
		'<div class="conteneur select_curation mid">
			<a href="./select_curation.php">
					<h2 class ="head"> Curation </h2>
					<p>Validate data of the database</p>
					<img src="./images/check.png" class = "image_index_1" alt="check">
			</a>
		</div>';
	?>

	<!-- Aide --> 
	<div class="conteneur rechercher_1 gauche">
		<a href="./rechercher_1.php">
				<h2 class ="head"> Search </h2>
				<p>Search data in the database</p>
				<img src="./images/recherche.png" class = "image_index_1" alt="recherche">
		</a>
	</div>
</div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<!--
<a href="./includes/stylesheet.css">style</a>
<a href="./includes/functions.js">javascript</a>
-->
