<!DOCTYPE html>
<html>
	<head>
		<?php
			// Show tittle name betwen <title> define default value if needed
			echo (!empty($title))?'<title>'.$title.'</title>':'<title> TriPod Database </title>';
		?>
		<meta charset="UTF-8">
		
		<!-- Stylesheet -->
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		
		<?php
		// Php functions
		include("functions_general.php");
		include("functions_validation_form.php");
		?>
		
		<!-- Javascript -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		
	</head>
	
	<body>
		<noscript> 
			<META HTTP-EQUIV="Refresh" CONTENT="0,URL='includes/erreurJs.php'"> 
		</noscript>
	
	<!-- Create cookies -->
	<?php	
	// Check if there is cookie or not
	if (isset ($_COOKIE['user'])) {
		$_SESSION['user'] = $_COOKIE['user']; 
		$user = $_SESSION['user']; //define $user with cookie value
	}
	else {
		// Attribution des variables de session
		$user=(isset($_SESSION['user']))?$_SESSION['user']:'';
	}
	if (isset ($_COOKIE['id'])) {
		$_SESSION['id'] = $_COOKIE['id']; 
		$idsess = 1;
	}
	else {
		// Attribution des variables de session
		$idsess=(isset($_SESSION['id']))?(int) $_SESSION['id']:0;
	}
	
	include("includes/header.php");
	include("includes/nav.php");

	// Temporary print
	//~ if (isset($_SESSION['user']) && isset($_SESSION['id'])) {
		//~ echo "session, user :".$_SESSION['user']."  //  ";
		//~ echo "session, idsess :".$_SESSION['id']."  //  ";
		//~ echo $idsess.$user;
		//~ echo '<br>';
	//~ }
	//~ else 
		//~ echo 'Pas de variables de session <br>';
	?>
	
