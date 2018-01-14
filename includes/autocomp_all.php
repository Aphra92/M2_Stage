<?php
include("dblog.php");
?>

<?php
// Après un appel ajax, autocompletion sur la colonne de la table actuelle
// autocompletion sur tout ce qui existe dans cette table
    if(isset($_POST['term'])) {
        // Mot tapé par l'utilisateur
        $where = htmlentities($_POST['term']);	
		$select = htmlentities($_POST['select']);
		$from = htmlentities($_POST['from']);
		$t = explode(',', $from);
		$from = array_shift($t);
		
		// On regarde si $where est splitable sur /
		$where = explode('/', $where);
		// On prend tjs le plus grand
		$where = array_pop($where);

        // Exécution de la requête SQL
        $requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' ORDER BY ".$select." LIMIT 50";
        $resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
		
		$term = array();
        // On parcourt les résultats de la requête SQL
        while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
			// On ajoute les données dans un tableau
            $term['val'][] = strval($donnees[$select]);
        }
        // On renvoie le données au format JSON pour le plugin
        echo json_encode($term);
	}
?>
