<!-- Menu de navigation disponible sur toutes les pages du site -->
<hr class ="style">
<nav id="nav-1" >
    <a class="link-1" href="./index.php">Home</a>
    <a class="link-1" href="./rechercher_1.php">Search</a>
    <a class="link-1" href="./Informations.pdf">Informations</a>
    <?php 
    if (!isset($_SESSION['id']))
	echo '<a class="link-1" href="./connexion.php">Log in</a>';
	if (isset($_SESSION['id']))
	echo '<a class="link-1" href="./deconnexion.php">Log out</a>';
	?>
</nav>
<hr class ="style">
<br>
