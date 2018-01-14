<?php
include("dblog.php");
?>

<?php
	// Après un appel ajax, le code retourne toutes les informations correspondant au protocole tapé par l'utilisateur qui 
	// est envoyé dans la variable term de $_POST
    if(isset($_POST['term'])) {
        // Mot tapé par l'utilisateur
        $proto = htmlentities($_POST['term']);	
        // Exécution de la requête SQL
        $requete = "SELECT * FROM tcr.protocole WHERE pr_name = '".$proto."'";
        $resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
		
		$term = array();
        // On parcourt les résultats de la requête SQL
        while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
			// On ajoute les données dans un tableau
            $term['lab'][] = strval($donnees["pr_val1_lab"]);
            $term['lab'][] = strval($donnees["pr_val1_obl"]);
            $term['lab'][] = strval($donnees["pr_val1_unit"]);
            $term['lab'][] = strval($donnees["pr_val2_lab"]);
            $term['lab'][] = strval($donnees["pr_val2_obl"]);
            $term['lab'][] = strval($donnees["pr_val2_unit"]);
            $term['lab'][] = strval($donnees["pr_val3_lab"]);
            $term['lab'][] = strval($donnees["pr_val3_obl"]);
            $term['lab'][] = strval($donnees["pr_val3_unit"]);
            $term['lab'][] = strval($donnees["pr_val4_lab"]);
            $term['lab'][] = strval($donnees["pr_val4_obl"]);
            $term['lab'][] = strval($donnees["pr_val4_unit"]);
            $term['lab'][] = strval($donnees["pr_val5_lab"]);
            $term['lab'][] = strval($donnees["pr_val5_obl"]);
            $term['lab'][] = strval($donnees["pr_val5_unit"]);
            $term['lab'][] = strval($donnees["pr_val6_lab"]);
            $term['lab'][] = strval($donnees["pr_val6_obl"]);
            $term['lab'][] = strval($donnees["pr_val6_unit"]);
            $term['lab'][] = strval($donnees["pr_val7_lab"]);
            $term['lab'][] = strval($donnees["pr_val7_obl"]);
            $term['lab'][] = strval($donnees["pr_val7_unit"]);
            $term['lab'][] = strval($donnees["pr_val8_lab"]);
            $term['lab'][] = strval($donnees["pr_val8_obl"]);
            $term['lab'][] = strval($donnees["pr_val8_unit"]);
        }

        // On renvoie le données au format JSON pour le plugin
        echo json_encode($term);
	}  
?>
