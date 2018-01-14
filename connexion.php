<?php
	session_start();
	//Include
	$title="Log In";
	include("includes/start.php");
	include("includes/dblog.php");
?>
	  
<br>
<?php
	// Form
	if (!isset($_POST['user'])) {
			echo '<form method="post" action="connexion.php">
			<fieldset>
				<legend>Connexion</legend>
				<p>
					<label for="user">User :</label><input name="user" type="text" id="user" /><br />
					<label for="password">Password :</label><input type="password" name="password" id="password" /><br>
					<label>Remember me ?</label><input type="checkbox" name="cookie" /><br />
				</p>
			</fieldset>
				<p><input type="submit" value="Connexion" /></p></form>
</body>
</html>';
	}
	// Message post envoi du formulaire
	else {
		// Si des champs sont pas rempli
		$message='';
		if (empty($_POST['user']) || empty($_POST['password']) ) { // Oublie d'un champ
			$message = '<p>Something went wrong during authentification. Please fill in all fields</p>
				<p>Clic  <a href="connexion.php">here</a> to try again</p>';
		}
		// Si tout est remplis
		else {
			// Check the user with kerberos
			// On check que le user et le mdp existent dans la base de donnée
			$query=$pdo->prepare('SELECT username, login, acces FROM users.users WHERE username = :user');
			$query->bindValue(':user',$_POST['user'], PDO::PARAM_STR);
			$query->execute();
			$data=$query->fetch();
			//echo $_POST['user'];
			//echo $_POST['password'];
			//~ if ($data['login'] == md5($_POST['password'])) { // Verification que le hash ecrit et le hash de la bdd correspond
			if ($data['login'] == $_POST['password']) {
				$_SESSION['user'] = $data['username'];
				$_SESSION['id'] = $data['acces'];
				$message = '<p>Bienvenue '.$_SESSION['user'].', 
					vous êtes maintenant connecté!</p>
					<p>Cliquez <a href="index.php">ici</a> 
					pour revenir à la page d accueil</p>';  
			}
			// Si le mot de passe ou l'identifiant est faux
			// Or If the user do not exist in kerberos
			else { 
				$message = '<p>Something went wrong
				during connexion.<br /> Password or user name are not correct.</p><p>Clic <a href="connexion.php">here</a> 
				to try again
				<br /><br />Clic <a href="index.php">here</a> to go back to welcome page</p>';
			}
			$query->CloseCursor();
		}
		// Affiche le message
		echo $message;
	}
?>

<?php
// Création d'un cookie
if (isset($_POST['cookie'])) {
	$expire = time() + 365*24*3600;
	setcookie('user', $_SESSION['user'], $expire);
	setcookie('id', $_SESSION['id'], $expire);  
}
?>

</body>
<?php
	include('./includes/footer.php');
?>
</html>
