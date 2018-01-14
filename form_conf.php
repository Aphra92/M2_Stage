<?php
	session_start();
	//Include
	$title="Add data";
?>

<?php
include("includes/start.php");
include("includes/dblog.php");
include("includes/functions_ajout_repertoire.php");
// Stock la variable contenant la base sur laquelle on requete
$schema = 'tcr';

// Sauvegarde le formulaire dans une variable de session
$_SESSION['save_form'] = $_POST;
//echo 'SESSION';
//~ p($_SESSION['save_form']);
	
if ((isset($_POST["save"]))) {	
	$t = explode(",",$_POST["save"]);
	$nbreq = array_pop($t);
}

// suppression de la sauvegarde de la ou on vient du tableau
// recup taille du tableau 
// recup le nombre de ligne originel du formulaire
array_pop($_POST);
$taille = sizeof($_POST);
$nbelem = $taille / $nbreq;

//~ echo 'nbelem/ligne / taille tableau / nb de requete <br>';
//~ echo $nbelem.'/'.$taille.'/'.$nbreq.'<br>';

// création d'un tableau de requete
// Chaque ligne du tableau correspond a une requete et chaque requete est composé d'un tableau contenant le nom
// de la colonne et la valeur associé
// Array ( [0] => Array ( [NOMTABLE] => Array ( [NOMCOLONNE] => VAL ....
$tabreq = array();
$listecol = $_POST;
//~ p($listecol);
foreach (array_keys($listecol) as $ln) {
	$t1 = explode("|", $ln);
	$row = $t1[1];
	$name = $t1[2];
	$val = $listecol[$ln];
	$t2 = explode(",", $t1[0]);
	// Ajout d'un tableau par ligne
	if (!array_key_exists($row, $tabreq)) {
		$tabreq[$row] = array();
	}
	// Recherche du nom des tables
	if (sizeof($t2) == 1) {
		$nametable = $t2[0];
		$tabreq = create_request_tab($nametable, $tabreq, $row, $val, $name);
	}
	else {
		for ($i = 0; $i < sizeof($t2); $i++){
			$nametable = $t2[$i];
			$tabreq = create_request_tab($nametable, $tabreq, $row, $val, $name);
		}
	}
}
//p($tabreq);
foreach (array_keys($tabreq) as $row) { // pour toutes les lignes (0,1,2 ...)
	$nameproj = '';
	$entity = array();
	foreach (array_keys($tabreq[$row]) as $table) { // pour toutes les requetes possibles dans la ligne 
		$next = true;
		// Si c'est repertoire, on l'ajoute uniquement si les fichiers existent
		if ($table == 'repertoire') {
			$next = checkfiles($tabreq, $pdo);
		}
		if ($next) {
			// (une requete ou une requete + une participation)
			//~ echo $table;
			// on construit la requete
			// Insert Into XX ( , ) VALUES ( , )
			// Les requetes créé sont pour remplir les tables et les particpes
			$reqstr = 'INSERT INTO '.$schema.'.'.$table.' ';
			$listcol = '(';
			$listval = '(';
			foreach (array_keys($tabreq[$row][$table]) as $key){
				$listcol .= $key.', ';
				$listval .= ':'.$key.', ';
			}
			$listcol = substr($listcol, 0, -2);
			$listval = substr($listval, 0, -2);
			$listcol .= ') VALUES ';
			$listval .= ');';
			$reqstr .= $listcol;
			$reqstr .= $listval;
			//~ echo $request;
			// on prepare la requete
			$request = $pdo->prepare($reqstr);
			// variable pour savoir si part_X contient une ou pusieurs valeurs
			$totpart = 0;
			// On parcours chaque valeur dans la ligne
			foreach (array_keys($tabreq[$row][$table]) as $key) {
				// On récupère le nom du projet pour la ligne en cours
				if (($key == 'p_id' || $key == 'p_id1') || ($key == 'p_id2' && $tabreq[$row][$table][$key] != '')) {
					$nameproj = $tabreq[$row][$table][$key];
				}
				// On split la clé pour faire des vérifications
				$start = explode('_', $key);
				// Si la clé est un identifiant, on ajoute la valeur dans le tableau
				if ($start[0] == 'resp') {
					$entity[] = $tabreq[$row][$table][$key];
				}
				// Si on est dans participe, on regarde tout les éléments
				if ($start[0] == 'part') {
					foreach (explode('/',$tabreq[$row][$table][$key]) as $val) {
						$totpart += 1;
						$entity[] = $val;
					}	
				}
				// Dans le cas général, on bind la valeur a la requete
				if ($start[0] != 'part') {
					$str =  $tabreq[$row][$table][$key];
					($str == '' ? $request -> bindValue(':'.$key,null, PDO::PARAM_STR) : 
						$request -> bindValue(':'.$key,$str, PDO::PARAM_STR));
				}
			}
			//p($entity);
			$addtoproject = array();
			// Transaction de la requete si on est pas en train d'ajouter une colonne participe
			// et ajout departicipe 
			if ($totpart == 0) {
				$previousexception = FALSE;
				try {	
					//echo "ajout entity /request";
					// on essai de créer une entity et les participations
					$pdo->beginTransaction();
					// create entity for resp
					if (isset($entity[0]))
						newentity($entity[0], $schema, $pdo);				
					$request -> execute();
					if (isset($entity[0]))
						$addtoproject[] = $entity[0];
					$pdo->commit();
				}
				catch (Exception $e) {
					$pdo->rollBack();
					try {
						//echo "ajout request <br>";
						$pdo->beginTransaction();
						$request -> execute();
						if (isset($entity[0]))
							$addtoproject[] = $entity[0];
						$pdo->commit();
					}
					catch (Exception $e) {
						$previousexception = TRUE;
						echo '<br> Erreur : ====>  '.$e;
						$pdo->rollBack();
						header("Location: ./form_newdata.php");
						exit;
					}
				}
			}	
			// createparticipant
			else {
				// on verifie que l'ajout dans la table n'a pa soulever une exception avant d'ajouter
				// les participations qui correpsondent
				if (isset($previousexception) && $previousexception == FALSE) {
					foreach ($entity as $ent) {
						//echo '<br>'.$ent;
						if ($table == 'participe_ac') {
							$request -> bindValue(':part_ac',$ent, PDO::PARAM_STR);
						}
						if ($table == 'participe_a') {
							$request -> bindValue(':part_a',$ent, PDO::PARAM_STR);
						}
						if ($table == 'participe_m') {
							$request -> bindValue(':part_m',$ent, PDO::PARAM_STR);
						}
						if ($table == 'participe_s') {
							$request -> bindValue(':part_s',$ent, PDO::PARAM_STR);
						}
						try {
							//echo "ajout entity / request<br>";
							$pdo->beginTransaction();
							newentity($ent, $schema, $pdo);
							$request -> execute();
							$addtoproject[] = $ent;
							$pdo->commit();
						}
						catch (Exception $e) {
							$pdo->rollBack();
							try {
								//echo "ajout request<br>";
								$pdo->beginTransaction();
								$request -> execute();
								$addtoproject[] = $ent;
								$pdo->commit();
							}
							catch (Exception $e) {
								echo '<br> Participant : ====>  '.$e;
								$pdo->rollBack();
							}
						}
					}
				}
			}
			foreach ($addtoproject as $add) {
				// Ajout des participants au projet
				try {
					$pdo->beginTransaction();
					if (isset($nameproj))
						newpartproj($nameproj, $add, $pdo);
					$pdo->commit();
				}
				catch (Exception $e) {
					echo '<br> Participant project : ====>  '.$e;
					$pdo->rollBack();
				}
			}
			if ($table == 'repertoire') {
				afterrepertoire($tabreq, $pdo);
			}
		}
		else {
			echo "files doesn't exists";
		}
	}
}
?>

<script>
  $( function() {
    $( document ).tooltip();
  } );
</script>
		
<script type="text/javascript" src="includes/functions.js"></script>
</body>
<?php
	include('./includes/footer.php');
?>
</html>
