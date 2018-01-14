<?php
include("dblog.php");
?>

<?php
	// Utilisé lors d'un appel ajax, elle permet de vérifier la présence ou non d'une action lié à un organism
	// La requete retourne toutes les ac_id pour un organisme donnée dans le but de vérifié si une de ces actions est le sampling
    if(isset($_POST['p'])) {
        // Mot tapé par l'utilisateur
        $p = htmlentities($_POST['p']);	
        $e = htmlentities($_POST['e']);	
        $o = htmlentities($_POST['o']);	
			
        // Connexion à la base de données

        // Exécution de la requête SQL
        $requete = "SELECT ac_id FROM tcr.action WHERE p_id1 = '".$p."'
        AND e_id1 = '".$e."'
        AND o_id1 = '".$o."'
        AND ac_type = 'sampling'
        ";
        $resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
		$aswr = $resultat->fetch();
		$val = $aswr['ac_id'];
        // On renvoie le données au format JSON pour le plugin
        if ($val == '')
			$val = 'empty';
        echo $val;
	}  
?>
