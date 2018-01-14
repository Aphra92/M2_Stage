<?php
include("dblog.php");
?>

<?php
// Autocompletion dynamique utilisé pour les clés. Pour chaque clé de la table, on regarde ce qui existe en fonction des combinaisons
// de clés antérieur qui sont entrés par l'utilisateur
    if(isset($_POST['term'])) {
        // Mot tapé par l'utilisateur
        $select = '';
        $where = htmlentities($_POST['term']);	
		$select = htmlentities($_POST['select']);
		$from = htmlentities($_POST['from']);
		$t = explode(',', $from);
		$from = array_shift($t);
		$curr = htmlentities($_POST['cur']);
		
		// On verifie que la valeur est entré et si la valeur est '' on met IS NOT NULL pour la requete d'autompletion
		if (isset($_POST['p'])) {
			($_POST['p'] == '' ? $p = 'IS NOT NULL' : $p = '= \''.$_POST['p'].'\'');
		}
		if (isset($_POST['e'])) {
			($_POST['e'] == '' ? $e = 'IS NOT NULL' : $e = '= \''.$_POST['e'].'\'');
		}
		if (isset($_POST['o'])) {
			($_POST['o'] == '' ? $o = 'IS NOT NULL' : $o = '= \''.$_POST['o'].'\'');
		}
		if (isset($_POST['s'])) {
			($_POST['s'] == '' ? $s = 'IS NOT NULL' : $s = '= \''.$_POST['s'].'\'');
		}
		if (isset($_POST['a'])) {
			($_POST['a'] == '' ? $a = 'IS NOT NULL' : $a = '= \''.$_POST['a'].'\'');
		}
		if (isset($_POST['m'])) {
			($_POST['m'] == '' ? $m = 'IS NOT NULL' : $m = '= \''.$_POST['m'].'\'');
		}
		if (isset($_POST['run'])) {
			($_POST['run'] == '' ? $r = 'IS NOT NULL' : $r = '= \''.$_POST['run'].'\'');
		}
		if (isset($_POST['rep'])) {
			($_POST['rep'] == '' ? $re = 'IS NOT NULL' : $re = '= \''.$_POST['rep'].'\'');
		}
		
		// listing de tout les cas possibles
		if ($curr == 'manip') {
			switch ($select) {
				case 'p_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND o_id ".$o." 
					AND s_id ".$s." 
					AND a_id ".$a." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
					AND p_id ".$p." 
					AND o_id ".$o." 
					AND s_id ".$s." 
					AND a_id ".$a." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'o_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND p_id ".$p." 
					AND s_id ".$s." 
					AND a_id ".$a." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 's_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
					AND e_id ".$e." 
					AND o_id ".$o." 
					AND p_id ".$p." 
					AND a_id ".$a." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'a_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND o_id ".$o." 
					AND s_id ".$s." 
					AND p_id ".$p." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'm_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND o_id ".$o." 
					AND s_id ".$s." 
					AND p_id ".$p." 
					AND a_id ".$a." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				default:
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
			}
		}
		if ($curr == 'action') {
			switch ($select) {
				case 'p_id1':
					$select = 'p_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND o_id ".$o." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id1':
					$select = 'e_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
					AND p_id ".$p." 
					AND o_id ".$o." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'o_id1':
					$select = 'o_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND p_id ".$p." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'p_id2':
					$select = 'p_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND o_id ".$o." 
					AND s_id ".$s."  
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id2':
					$select = 'e_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
					AND p_id ".$p." 
					AND o_id ".$o." 
					AND s_id ".$s." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'o_id2':
					$select = 'o_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND p_id ".$p." 
					AND s_id ".$s."  
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 's_id2':
					$select = 's_id';
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e." 
					AND p_id ".$p." 
					AND o_id ".$o." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				default:
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
					ORDER BY ".$select." DESC LIMIT 20";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
			}
		}
		if ($curr == 'protocole') {
			$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
			ORDER BY ".$select." LIMIT 50";
			$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
		}
		if ($curr == 'run') {
			$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
			ORDER BY ".$select." LIMIT 50";
			$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
		}
		if ($curr == 'project') {
			$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%' 
			ORDER BY ".$select." LIMIT 50";
			$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
		}
		if ($curr == 'experiment') {
			switch ($select) {
				case 'p_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND p_id ".$p." 
					ORDER BY ".$select." DESC LIMIT 20";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
			}
		}
		if ($curr == 'organism') {
			switch ($select) {
				case 'p_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND p_id ".$p." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'o_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND p_id ".$p." 
					AND e_id ".$e." 
					ORDER BY ".$select." DESC LIMIT 20";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
			}
		}
		if ($curr == 'sample') {
			if ($select == 'p_id') {
				$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
				AND e_id ".$e." 
				AND o_id ".$o." 
				ORDER BY ".$select." LIMIT 50";
				$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
			}
			else {
				if ($select == 'e_id') {
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND p_id ".$p." 
					AND o_id ".$o." 
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
				}
				else {
					if ($select == 'o_id') {
						$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
						AND p_id ".$p." 
						AND e_id ".$e." 
						ORDER BY ".$select." LIMIT 50";
						$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					}
					else {
						if ($select == 's_id') {
							$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
							AND p_id ".$p." 
							AND e_id ".$e."
							AND o_id ".$o."
							ORDER BY ".$select." DESC LIMIT 20";
							$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
						}
						else {
							if ($select == 's_exp_group1') {
								$select = "o_exp_group1";
								// Cette requete ne fonctionne pas
								// Elle est automplete_all mnt
								/*$pid = 'TriPoD';
								$eid = '1';
								$requete = "SELECT o_exp_group1 FROM tcr.organism WHERE p_id = :pid AND e_id = :eid";
								$resultat = $pdo->prepare($requete);
								$resultat -> bindValue(':pid',$p, PDO::PARAM_STR); 
								$resultat -> bindValue(':eid',$e, PDO::PARAM_STR);
								$resultat -> execute();*/
								/*$requete = "SELECT distinct(o_exp_group1) FROM tcr.organism WHERE CAST(o_exp_group1 AS TEXT) ilike '"."$where"."%'
								AND p_id ".$p." 
								AND e_id ".$e."
								AND o_id ".$o."
								ORDER BY o_exp_group1 LIMIT 10";
								$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));*/
							}
						}
					}
				}
			}
		}
		if ($curr == 'aliquot') {
			switch ($select) {
				case 'p_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND s_id ".$s."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND p_id ".$p."
					AND o_id ".$o."
					AND s_id ".$s."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'o_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND p_id ".$p."
					AND s_id ".$s."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 's_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND p_id ".$p."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'a_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND p_id ".$p."
					AND s_id ".$s."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
			}
		}
		if ($curr == 'repertoire') {
			switch ($select) {
				case 'p_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND s_id ".$s."
					AND a_id ".$a."
					AND m_id ".$m."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'e_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND p_id ".$p."
					AND o_id ".$o."
					AND s_id ".$s."
					AND a_id ".$a."
					AND m_id ".$m."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'o_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND p_id ".$p."
					AND s_id ".$s."
					AND a_id ".$a."
					AND m_id ".$m."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 's_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND p_id ".$p."
					AND a_id ".$a."
					AND m_id ".$m."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'a_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND s_id ".$s."
					AND p_id ".$p."
					AND m_id ".$m."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'm_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND s_id ".$s."
					AND a_id ".$a."
					AND p_id ".$p."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'run_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
				case 'rep_id':
					$requete = "SELECT distinct(".$select.") FROM tcr.".$from." WHERE CAST(".$select." AS TEXT) ilike '"."$where"."%'
					AND e_id ".$e."
					AND o_id ".$o."
					AND s_id ".$s."
					AND a_id ".$a."
					AND p_id ".$p."
					AND m_id ".$m."
					ORDER BY ".$select." LIMIT 50";
					$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
					break;
			}
		}
		if ($curr == 'repertoire') {
			if ($select == 'n_ana') {
				$requete = "SELECT distinct(n_ana) FROM tcr.repertoire 
				WHERE p_id ".$p." 
				AND CAST(e_id AS TEXT) ".$e." 
				AND CAST(o_id AS TEXT) ".$o." 
				AND CAST(s_id AS TEXT) ".$s." 
				AND CAST(a_id AS TEXT) ".$a." 
				AND CAST(m_id AS TEXT) ".$m." 
				AND CAST(run_id AS TEXT) ".$r."			
				";
				$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
			}
		}
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
