<?php
	session_start();
	// destroy cookie
	if (isset ($_COOKIE['user'])) {
		setcookie('user','', -1);
		setcookie('id','', -1);
	}
	session_destroy();
	$titre="Log Out";
	include("includes/start.php");

// Affiche un message de déconnexion
echo '<p>You are now deconnected <br />
	Clic <a href="index.php">here</a> to go back to welcome page</p>';
?>

</body>
<?php
	include('./includes/footer.php');
?>
</html>
