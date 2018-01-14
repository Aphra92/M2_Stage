<?php
// recupération de toutes les informations necessaire dans la base pour créer un fichier cl2 
// pour les repertoires demandé et génération d'un fichier
// ligne par ligne
	include("dblog.php");
	
	$schema ='tcr';
	//~ $schema ='tcr';
	
	//~ p($_POST);
	$type = $_POST["type"];
	$coll = $_POST["coll"];
	if ($type == 'DNA') {
		if ($coll == 'notcollapse')
			$sel = 'countdna';
		if ($coll == 'collapsed')
			$sel = 'countdna_after_col';
		$seq = 'tcr_cdr3nt';
		$distinct = '';
	}
	if ($type == 'AminoAcid'){
		if ($coll == 'notcollapse')
			$sel = 'countpep';
		if ($coll == 'collapsed')
			$sel = 'countpep_after_col';
		$seq = 'tcr_cdr3aa';
		$distinct = 'distinct on (tcr_cdr3aa)';
	}
	$lenmyInputs = sizeof($_POST["myInputs"]);
	$rep = $_POST["myInputs"];
	//~ echo $type.$lenmyInputs;
	
	$aswr = array();
	for ($i = 0; $i < $lenmyInputs; $i++) {
		$key = explode(',',$rep[$i]);
		$query = 'SELECT '.$distinct.' tcr_obs.tcr_id,tcr_vlabel,'.$seq.',tcr_jlabel,'.$sel.',rep_id FROM '.$schema.'.tcr_obs, '.$schema.'.tcr WHERE 
		p_id = \''.$key[0].'\'
		AND e_id = \''.$key[1].'\'
		AND o_id = '.$key[2].'
		AND s_id = \''.$key[3].'\'
		AND a_id = '.$key[4].'
		AND m_id = \''.$key[5].'\'
		AND run_id = \''.$key[6].'\'
		AND rep_id = \''.$key[7].'\'
		AND n_ana = '.$key[8].'
		AND tcr.tcr_id = tcr_obs.tcr_id;';
		//~ echo $query.'<br>';
		$request = $pdo->prepare($query);
		$request -> execute();

		while($donnees = $request->fetch(PDO::FETCH_ASSOC)) {
			// On ajoute les données dans un tableau
			$aswr['tcr_id'][] = $donnees['tcr_id'];
			$aswr['tcr_vlabel'][] = $donnees['tcr_vlabel'];
			$aswr[$seq][] = $donnees[$seq];
			$aswr['tcr_jlabel'][] = $donnees['tcr_jlabel'];
			$aswr[$sel][] = $donnees[$sel];
			$aswr['rep_id'][] = $donnees['rep_id'];
        }
	}
	// Génération du fichier ligne par ligne
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=CL.txt");
	$keys = array();
	$title = "\"ID\"\t\"lib\"\t\"V\"\t\"pep\"\t\"J\"\t\"count\"";
	foreach (array_keys($aswr) as $k) {
		$keys[] = $k;
	}
	//~ p($aswr);
	//~ p($keys);
	echo $title."\n";
	for ($i = 0; $i < sizeof($aswr['tcr_id']); $i++) {
		echo ($aswr[$keys[4]][$i] == '' ? '':
					'"'.$aswr[$keys[1]][$i].' '.$aswr[$keys[2]][$i].' '.
					$aswr[$keys[3]][$i].'"'."\t\"".$aswr[$keys[5]][$i]."\"\t".
					'"'.$aswr[$keys[1]][$i].'"'."\t".'"'.$aswr[$keys[2]][$i].'"'."\t".'"'.$aswr[$keys[3]][$i].'"'."\t".
					$aswr[$keys[4]][$i]."\n");
				//~ echo ($aswr[$keys[4]][$i] == '' ? '0': $aswr[$keys[4]][$i])."\n";
	}
?>
