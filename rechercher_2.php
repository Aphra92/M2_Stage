<?php
	include("includes/start.php");
	include("includes/dblog.php");
	include("includes/functions_search.php");
?>
<script>
function inchoice(div) {
	//~ alert(div);
	var col = div.split('_');
	col = col[1];
	// hide & compress initial text
	$('.link_'+col).stop().animate({
		width: '0px',
		opacity: 0
	},$('.link_'+col).hide);
	// show & decompress link options
	$('.tomodif_'+col).stop().show().animate({
		width: '100px',
		opacity: 1
	});
}
function outchoice(div) {
	var col = div.split('_');
	col = col[1];
	// hide & compress options
	$('.tomodif_'+col).stop().animate({
		width: '0px',
		opacity: 0
	}, $('.tomodif_'+col+' a').hide);
	// show & decompress link options
	$('.link_'+col).stop().show().animate({
		width: '200px',
		opacity: 1
	});
}
</script>

<?php
// Créé un tableau contenant les requetes en fonction de ce qui est recu dans $_POST
function config_request_tab($POST) {
	$tabrequest = array();
	//~ p($POST);
	// Si on recoit un formulaire simple
	if (array_key_exists('table_a', $POST))
		$tabrequest[1][1] = $POST['table_a'].','.$POST['colonne_a'].','.$POST['choice_a'].','.$POST['value_a'];
	else { // Si c'est un formulaire combinable
		$nbreq = 1; // 
		$start = 1; // 
		$i = 1; // cpt nb de ligne du formulaire
		$j = 1; // cpt pour parcourir le tableau initiale
		//~ echo sizeof($POST);
		while ($j < sizeof($POST)) {
			// Si on voit un bloc on change de requete
			if (isset($POST['bloc_'.$i])) {
				$start = 1;
				$nbreq++;
				$j++;
				if (isset($POST['sel_'.$i])) {
					$tabrequest[$nbreq][$start] = $POST['table_'.$i].','.$POST['colonne_'.$i].','.$POST['choice_'.$i]
						.','.$POST['value_'.$i].','.$POST['sel_'.$i];
					$j += 5;				
				}
				else {
					$tabrequest[$nbreq][$start] = $POST['table_'.$i].','.$POST['colonne_'.$i].','.$POST['choice_'.$i]
						.','.$POST['value_'.$i].','.' ';
					$j += 4;
				}
			}
			if (isset($POST['sel_'.$i])) {
				$tabrequest[$nbreq][$start] = $POST['table_'.$i].','.$POST['colonne_'.$i].','.$POST['choice_'.$i]
					.','.$POST['value_'.$i].','.$POST['sel_'.$i];
				$j += 5;				
			}
			else {
				$tabrequest[$nbreq][$start] = $POST['table_'.$i].','.$POST['colonne_'.$i].','.$POST['choice_'.$i]
					.','.$POST['value_'.$i].','.' ';
				$j += 4;
			}
			$i++;
			$start++;			
		}
	}
	//~ p($tabrequest);
	return $tabrequest;
	// return un tableau contenant un tableau par requete. Chaque entré du tableau de requete contient un str pouvant etre split sur la ,
}

function create_constraints_request($cont, $pdo, $schema, $tabkeys, $f) {
	$constraints = array();
	$constraints['keys'] = array();
	$constraints['table'] = array();
	$temp1 = array(); // stock la clé
	$temp2 = array(); // stock la table d'origine
	// On parcours les tables sur lesquels on recherche
	foreach ($cont as $val) {
		// recuperation du nom des colonnes de chaque table
		$columnnames = get_columns_names($pdo, $schema, $val);
		foreach ($columnnames[$val] as $col){
			// Si c'est une clé, on l'ajoute a temp
			if (in_array($col, $tabkeys)){
				array_push($constraints['keys'],$col);
				array_push($constraints['table'],$val);
			}
		}
	}
	if ($f == 0) {
		// Ajout tcr_obs si besoin
		if (in_array('tcr',$constraints['table']) && !in_array('tcr_obs',$constraints['table'])) {
			$columnnames = get_columns_names($pdo, $schema, 'tcr_obs');
			foreach ($columnnames['tcr_obs'] as $col){
				if (in_array($col, $tabkeys)){
					array_push($constraints['keys'],$col);
					array_push($constraints['table'],'tcr_obs');
				}
			}
		}
		// Ajout de repertoire si besoin
		if ((in_array('project',$constraints['table']) || in_array('experiment',$constraints['table']) || 
			in_array('organism',$constraints['table']) ||
			in_array('sample',$constraints['table']) || in_array('aliquot',$constraints['table']) ||
			 in_array('manip',$constraints['table']))
			&& (in_array('run',$constraints['table']) || in_array('tcr_obs',$constraints['table']) ||
			 in_array('stat',$constraints['table']))) {
			if (!in_array('repertoire',$constraints['table'])) {
				// Dans ce cas, on ajoute repertoire dans la recherche 
				$columnnames = get_columns_names($pdo, $schema, 'repertoire');
				foreach ($columnnames['repertoire'] as $col){
					if (in_array($col, $tabkeys)){
						array_push($constraints['keys'],$col);
						array_push($constraints['table'],'repertoire');
					}
				}
			}
		}
	}
	return $constraints;
}

function create_from($cont, $schema) {
	$tempfrom = array();
	foreach ($cont as $f)
		switch($f) {
			case 'project':
				$tempfrom[1] = $f;
				break;
			case 'experiment':
				$tempfrom[2] = $f;
				break;
			case 'organism':
				$tempfrom[3] = $f;
				break;
			case 'sample':
				$tempfrom[4] = $f;
				break;
			case 'aliquot':
				$tempfrom[5] = $f;
				break;
			case 'manip':
				$tempfrom[6] = $f;
				break;
			case 'run':
				$tempfrom[7] = $f;
				break;
			case 'tcr_obs':
				$tempfrom[8] = $f;
				break;
			case 'tcr':
				$tempfrom[9] = $f;
				break;
			case 'stat':
				$tempfrom[10] = $f;
				break;
			case 'repertoire':
				$tempfrom[11] = $f;
				break;
			// Pour la recherche simple
			default:
				$tempfrom[] = $f;
		}
	// tri le tableau par clé
	//~ p($cont);
	ksort($tempfrom);
	//~ p($tempfrom);
	$from = '';
	foreach ($tempfrom as $f) {
		if ($from == '')
			$from .= $schema.'.'.$f;
		else 
			$from .= ', '.$schema.'.'.$f;
	}
	$temp = array();
	$temp['cont'] = $tempfrom;
	$temp['from'] = $from;
	return $temp;
}

function create_jointure($constraints, $where) {
	// on regarde les clés en double
	$double = array_count_values($constraints['keys']);
	$join = '';
	// On parcours toutes les clés dans double
	foreach (array_keys($double) as $d) {
		if ($double[$d] > 1) {
			// on parcours le tableau 1
			for ($i=0; $i < sizeof($constraints['keys']); $i++) {
				// compteur pour savoir le nb de match
				$nb = 0;
				// On commence par AND pour le mettre a la suite des conditions demandés
				if ($constraints['keys'][$i] == $d) {
					$nb++;
					// on part de la position i+1 pour trouver les autres occurences
					for ($j=$i+1; $j < sizeof($constraints['keys']); $j++) {
						if ($constraints['keys'][$j] == $d) {
							$nb++;
							if ($nb != 1) {
								$join .= $constraints['table'][$i].".".$constraints['keys'][$i]." = 
										".$constraints['table'][$j].".".$constraints['keys'][$j].' AND ';
							}
						}
					}
				}
			}
		}
	}
	$jointure = substr($join, 0, -4);
	return $jointure;
}

function create_select($cont, $pdo, $schema) {
	$select = '';
	foreach ($cont as $table) {
		$colonnes = get_columns($pdo, $schema, $table);
		foreach (array_keys($colonnes) as $k) {
			$select .= $colonnes[$k].'.'.$k.', ';
		}
	}
	$select = substr($select, 0, -2);
	return $select;
}

// Défini le schéma de la base
$schema ='tcr';
$notin = '';
// récupération des clés de la table
$tabkeys = get_liste_key($pdo, $schema);
//~ p($tabkeys);

// Transformation des données recu en un tableau contenant pour entré une serie de valeur séparé par une virgule
$tabrequest = config_request_tab($_POST);
//~ echo '<br> Tableau de la requete <br>';
//~ p($tabrequest);
$listquery = array(); 	// tableau pour garder la liste des requetes 
$cont = array();		// tableau contenant la liste des contraintes pour chaque requete
$send_cont = array();	//tableau contenant la liste des contraintes global
// Parcours chaque requete du tableau précédent
for ($j = 1; $j <= sizeof($tabrequest); $j++) {
	$cont[$j] = array();
	$listquery[$j] = array();
	$where = ' '; //partie temporaire du where
	for ($i = 1; $i <= sizeof($tabrequest[$j]); $i++) {
		$temp = explode(',', $tabrequest[$j][$i]);
		$table = $temp[0];
		$columnname = $temp[1];
		$val = $temp[3];
		$wh = $temp[4];
		if ($val == null)
			$val = '';
		
		// On ajoute la table actuelle dans les deux tableau de sauvegarde	
		if (!in_array($table, $cont[$j]))
			$cont[$j][] = $table;
		if (!in_array($table, $send_cont))
			$send_cont[] = $table;
		
		// On crée les where en fonction des requetes
		if ($temp[2] != 'like' && $temp[2] != 'is null' && $temp[2] != 'is not null' && $temp[2] != '*')
			$where .= ' '.$wh.' '.$schema.'.'.$table.".".$columnname.' '.$temp[2]." '".$val."'";
		else {
			if ($temp[2] == 'like') 
				$where .= 'CAST('.$schema.'.'.$table.".".$columnname.' AS TEXT) ilike \'%'.$val.'%\' AND ';
			if ($temp[2] == 'is null' || $temp[2] == 'is not null') 
				$where .= ' '.$wh.' '.$schema.'.'.$table.".".$columnname.' '.$temp[2];
			if ($temp[2] == '*') 
				$where .= '';
		}
	}
	//~ echo '<br>'.$where;
	//~ echo substr($where, 0, 5);
	if (substr($where, 0, 5) == "  AND")
		$where = substr($where, 5);
	if (substr($where, 0, 4) == "  OR")
		$where = substr($where, 4);

	//////////////////////////////////////
	// Creation des contraintes
	// Verification de la continuité
	// Ajout automatique de tcr_obs pour simplifier si on requete sur tcr
	// Ajout automatique du repertoire pour faire le lien si on requete a la fois sur protocole et sur les tcr
	// Si on requete dans tcr, on ajoute automatiquement tcr_obs pour simplifier
	$constraints = create_constraints_request($cont[$j], $pdo, $schema, $tabkeys, $j-1);
	//~ p($constraints);
	// Ajout de tcr_obs dans $cont et $send_cont si $constraints['table'] contient 'tcr_obs'
	if (in_array('tcr_obs', $constraints['table']) && !in_array('tcr_obs', $cont)) {
		array_push($cont[$j],'tcr_obs');
		array_push($send_cont,'tcr_obs');
	}
	// Ajout de repertoire dans $cont et $send_cont si $constraints['table'] contient 'repertoire'
	if (in_array('repertoire', $constraints['table']) && !in_array('repertoire', $cont)) {
		array_push($cont[$j],'repertoire');
		array_push($send_cont,'repertoire');
	}
	//////////////////////////////////////
	
	// Création du from dans le bon ordre et actualisation de $cont
	$result = create_from($cont[$j], $schema);
	//~ p($result);
	$from = $result['from'];
	$cont[$j] = $result['cont'];
	
	//////////////////////////////////////
	//creation des jointures
	$jointure = create_jointure($constraints, $where);
	//~ echo $jointure;
	//concatenation des jointures avec le where actuelle
	if ($where == ' ' && $jointure == '') {
		$where == '';
	}
	else {
		if ($where == ' ' && $jointure != '') {
			$where = 'WHERE '.$jointure;
		}
		else {
			if ($where != ' ' && $jointure == '')
				$where = 'WHERE '.$where;
			else 
				$where = 'WHERE '.$where.' AND '.$jointure;
		}
	}
	//~ echo $where.'<br>';
	//////////////////////////////////////
	
	// Ajout de la requete final dans le tableau des requetes
	// Génération du Select
	$select = create_select($cont[$j], $pdo, $schema);
	// Ecriture du from
	$from = 'FROM '.$from;
	// Concatenation
	$query = 'SELECT '.$select.' '.$from.' '.$where.';';
	// Ajout 
	//~ echo $query;
	$listquery[$j] = $query;

	
}
//p($listquery);
//~ p($cont);
//~ p($send_cont);

$listquery[1] = str_replace('AND ;', ';', $listquery[1]);

// Génération de la requete final a partir du tableau $listquery
// Si le tableau contient une seule requete elle est envoyé
// Si elle en contient plus c'est qu'il y a des bloc différent et elle est traité
if (sizeof($listquery) == 1)
	$request = $pdo->query($listquery[1]);
else {
	// On parocrous le tableau en partant de la requete externe
	//p($listquery[1]);
	for ($i = sizeof($listquery); $i > 0; $i--) {
		$q = $listquery[$i];
		//~ p($i);
		//~ p($q);
		// Si la requete 1 n'a pas de where on le rajoute
		if (!preg_match('/WHERE/', $listquery[1]))
			$listquery[1] = str_replace(';', 'WHERE ;', $listquery[1]);	
		// Si la requete en cours n'est pas la derniere
		if ($i > 1) {
			// On récupère dans un tableau les tables pour lesquel on doit ajouter des jointures
			$todo = array();
			$copycont = $cont;
			//~ p($copycont);
			$todo[] = array_pop($copycont[$i]);
			$todo[] = array_pop($copycont[1]);
			$constraints = create_constraints_request($todo, $pdo, $schema, $tabkeys, 1);
			//~ p($constraints);
			
			// POTENTIELLEMENT NON UTILE OU PROBLEMATIQUE ICI ==> A VOIR
			// Ajout de tcr_obs dans $cont et $send_cont si $constraints['table'] contient 'tcr_obs'
			//~ if (in_array('tcr_obs', $constraints['table']) && !in_array('tcr_obs', $cont)) {
				//~ array_push($cont[$j],'tcr_obs');
				//~ array_push($send_cont,'tcr_obs');
			//~ }
			//~ // Ajout de repertoire dans $cont et $send_cont si $constraints['table'] contient 'repertoire'
			//~ if (in_array('repertoire', $constraints['table']) && !in_array('repertoire', $cont)) {
				//~ array_push($cont[$j],'repertoire');
				//~ array_push($send_cont,'repertoire');
			//~ }
			//creation des jointures
			$jointure = create_jointure($constraints, '');
			//~ p($jointure);
			
			///////////////////////////////////
			///////////////////////////////////
			///////////////////////////////////
			// A voir pour généralisé
			$q = preg_replace('/SELECT .*? FROM/', 'LEFT OUTER JOIN', $q);
			$temp = explode('WHERE', $q);
			$on = $temp[0];
			$wh = $temp[1];
			//~ p($on.'ON '.$jointure);
			//~ p('AND'.$wh);
			$listquery[1] = str_replace(';', 'AND'.$wh, $listquery[1]);	
			$listquery[1] = str_replace('WHERE', $on.'ON '.$jointure.'WHERE', $listquery[1]);	
		}
	}
	$listquery[1] = str_replace('SELECT', 'SELECT distinct', $listquery[1]);
	$listquery[1] = str_replace('WHERE AND', 'WHERE', $listquery[1]);
	$prefinal = $listquery[1];
	/////////////////////////////////////////////
	// Parenthèsage de la requete
	$temp = explode('WHERE', $prefinal);
	$p1 = $temp[0];
	$p2 = $temp[1];
	$p2 = explode('AND', $p2);
	for ($i = 0; $i < sizeof($p2); $i++) {
		$pattern1 = '/OR/';
		$pattern2 = '/;/';
		if (preg_match($pattern1, $p2[$i])) {
			if (preg_match($pattern2, $p2[$i])) {
				$p2[$i] = str_replace(';',' ) ;',$p2[$i]);
				$p2[$i] = '( '.$p2[$i];
			}
			else 
				$p2[$i] = '('.$p2[$i].')';
		}
	}
	$wh = '';
	foreach ($p2 as $p) {
		if ($wh == '')
			$wh .= $p;
		else 
			$wh .= ' AND '. $p;
	}
	$final = $p1.' WHERE '.$wh;
	//echo '<br>'.$final;
	/////////////////////////////////////////////
	
	$request = $pdo->query($final);
}

// Création du tableau de resultats à partir de la requete
//~ $tabfirst = mise_en_form_result($request, $schema, $pdo, $send_cont);
$tabfirst = mise_en_form_result($request, $schema, $pdo, $cont[1]);
?>

<script>	
	$('#colname').append('<?php echo $tabfirst; ?>');
</script>

<?php	
function mise_en_form_result($request, $schema, $pdo, $temp2) {
	// récupération des clés
	$keys = get_liste_key($pdo, $schema);
	
	// Tableau de correspondance utilisé pour les titres
	$corres = array(
		"p_id" => "Project",
		"e_id" => "Experiment",
		"o_id" => "Organism",
		"s_id" => "Sample",
		"a_id" => "Aliquot",
		"m_id" => "Manip",
		"run_id" => "Run",
		"rep_id" => "Repertoire",
		"tcr_id" => "TCR",		
		"pr_name" => "Protocole",		
		"ac_id" => "Action",		
		"st_label" => "Stat",		
		"part_ac" => "Participe_ac",		
		"part_s" => "Participe_sample",		
		"part_a" => "Participe_a",		
		"part_proj" => "Participe_proj",		
		"part_m" => "Participe_m",	
		"n_ana" => "Repertoire",	
	);
	// tableau de correspondance entre l'id et la table
	$corres_id_table = array(
		"p_id" => "project",
		"e_id" => "experiment",
		"o_id" => "organism",
		"s_id" => "sample",
		"a_id" => "aliquot",
		"m_id" => "manip",
		"run_id" => "run",
		"rep_id" => "repertoire",
		"tcr_id" => "tcr",		
		"pr_name" => "protocole",		
		"ac_id" => "action",		
		"st_label" => "stat",		
		"part_ac" => "",		
		"part_s" => "",		
		"part_a" => "",		
		"part_proj" => "",		
		"part_m" => "",	
		"n_ana" => "repertoire",
	);
	
	// Création d'un tableau contenant le resultat de la requete
	$tcr = array();
	while($row = $request->fetch(PDO::FETCH_ASSOC)) {
		$tcr[] = $row;
	}
	$le = sizeof($tcr);
	//~ p($tcr);
	// Si le tableau est vide on affiche un message
	if ($le == 0)
		echo "Désolé! Aucun résultat n'a été trouvé dans la base de données<br>";
	else 
		echo '<br>'.$le.' results found <br>';
	
	
	// TABLEAU DES RESULTATS //
	/*************************/
	echo '<div class="divcenter">';
	echo '<div id ="resultresearch">';
	echo '<div id = "colname">';
	echo '</div>';
	echo '<div id = "resultvalue">';
	$first = true; //variable pour savoir si on doit remplir le tableau contenant le nom des colonnes
	// initialisation cpt utilisé pour les class de la modification
	$cpt = 1;
	// tableau contenant les titres
	$tab_title_gauche = array();
	// tableau contenant les case a sauvegarder
	$save_case = array();
	$tabfirst = ''; // contient les td du tableau a placé a gauche
	
	// Parcours de toutes les lignes de requete dans TCR (0,1,2 etc)
	foreach (array_keys($tcr) as $i) {
		$temp = '';
		$save_repid = ''; 	//Pour le cas spécifique du repertoire
		$save_repval = ''; 	//Pour le cas spécifique du repertoire
		$tempmanip = '';	//Pour permettre de navigué dans les manip
		$tempac1 = '';		//Pour permettre de navigué dans les action (organisme)
		$tempac2 = '';		//Pour permettre de navigué dans les action	(sample)
		echo '<div class ="resultpage">';
		echo '<table class="resultp">';
		// Pour toutes les clés contenu dans chaque ligne
		foreach (array_keys($tcr[$i]) as $k) {			
			$val = $tcr[$i][$k];
			// si la valeur n'est pas une clé //
			/**********************************/
			if (!in_array($k, $keys)) { 
				// Si on est dans manip, cas particulié pour faire un lien vers la manip précédente
				if ($k == 'p_idprev' || $k == 'e_idprev' || $k == 'o_idprev' || $k == 's_idprev' || $k == 'a_idprev' || $k == 'm_idprev') {
					// on sauvegarde au fur et a mesure
					if ($tempmanip == '')
						$tempmanip .= $val;
					else 
						$tempmanip .= ','.$val;
					// cas normal, on ajoute les deux cases
					if ($k != 'm_idprev') {
						if ($first == true) {
							$tabfirst .= add_to_title_gauche($k);
						}
						echo create_case($val);
					}
					else { // cas final on ajoute les deux cases, un lien avec le tempmanip
						if ($first == true) {
							$tabfirst .= add_to_title_gauche($k);
						}
						echo create_case_key('m_id', $val, $tempmanip);
					}
				}
				// Cas normal, on affiche les cases et la cases titre qui correspond si besoin
				else {
					if ($k == 'p_id1' || $k == 'e_id1' || $k == 'o_id1') {
						// on sauvegarde au fur et a mesure
						if ($tempac1 == '')
							$tempac1 .= $val;
						else 
							$tempac1 .= ','.$val;
						
						// cas normal, on ajoute les deux cases
						if ($k != 'o_id1') {
							if ($first == true) {
								$tabfirst .= add_to_title_gauche($k);
							}
							echo create_case($val);
						}
						else { // cas final on ajoute les deux cases, un lien avec le tempmanip
							if ($first == true) {
								$tabfirst .= add_to_title_gauche($k);
							}
							echo create_case_key('o_id', $val, $tempac1);
						}
					}
					else {
						if ($k == 'p_id2' || $k == 'e_id2' || $k == 'o_id2' || $k == 's_id2') {
							// on sauvegarde au fur et a mesure
							if ($tempac2 == '')
								$tempac2 .= $val;
							else 
								$tempac2 .= ','.$val;
							
							// cas normal, on ajoute les deux cases
							if ($k != 's_id2') {
								if ($first == true) {
									$tabfirst .= add_to_title_gauche($k);
								}
								echo create_case($val);
							}
							else { // cas final on ajoute les deux cases, un lien avec le tempmanip
								if ($first == true) {
									$tabfirst .= add_to_title_gauche($k);
								}
								echo create_case_key('s_id', $val, $tempac2);
							}
						}
						else {
							switch ($k) {
								case 'p_start':
									// Pour chaque cas, si c'est la premiere colonne on ajoute le titre
									// ensuite on echo le titre 
									// on echo la case actuelle
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['p_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['p_id'];
									echo create_case($val);
									break;
								case 'e_comment':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['e_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['e_id'];
									echo create_case($val);
									break;
								case 'o_exp_in':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['o_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['o_id'];
									echo create_case($val);
									break;
								case 's_organ':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['s_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['s_id'];
									echo create_case($val);
									break;
								case 'a_cellnb':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['a_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['a_id'];
									echo create_case($val);
									break;
								case 'm_type':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['m_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['m_id'];
									echo create_case($val);
									break;
								case 'run_sequencer':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['run_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['run_id'];
									echo create_case($val);
									break;
								case 'st_value':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['st_label'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['st_label'];
									echo create_case($val);
									break;
								case 'tcr_chain':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['tcr_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['tcr_id'];
									echo create_case($val);
									break;
								case 'pr_type_tag':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['pr_name'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['pr_name'];
									echo create_case($val);
									break;
								case 'ac_exp_d':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['ac_id'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['ac_id'];
									echo create_case($val);
									break;
								case 'rep_location':
									if ($first == true) {
										$tabfirst .= $tab_title_gauche['rep_id'];
										$tabfirst .= $tab_title_gauche['n_ana'];
										$tabfirst .= add_to_title_gauche($k);
									}
									echo $save_case['rep_id'];
									echo $save_case['n_ana'];
									echo create_case($val);
									break;
								default:
									// On ajoute au tableau des titres la colonne actuelle
									if ($first == true) {
										$tabfirst .= add_to_title_gauche($k);
									}
									// on affiche les valeurs
									echo create_case($val);
							}
						}
					}
				}
			}
			// Si la valeur est une clé c'est un lien //
			/******************************************/
			else { 
				// On ajoute a temp la clé actuelle si besoin 
				if ($k != 'tcr_id' && $k != 'pr_name') {
					if ($temp == '')
						$temp .= $val;
					else {
						if (substr($val, 0, 1) == "#") {
							$val = substr($val, 1);
							//~ echo $val;
						}
						$temp .= ','.$val;
					}
				}
				// Definition de $link
				/*********************/
				if ($k != 'tcr_id' && $k != 'pr_name' && $k != 'run_id')
					$link = $temp;
				else 
					$link = $val;
					
				// Si c'est la premiere colonne
				// On ajoute a la colonne de gauche la colonne actuelle de la base
				if ($first == true && $k != 'n_ana') {
					$tab_title_gauche[$k] = add_to_title_gauche_title($k);
				}
				// Cas general
				if ($k != 'n_ana' && $k != 'rep_id') {
					$title = $corres[$k];
					$save_case[$k] = create_title($title);
					// Si l'utilisateur est superadmin
					if (isset($_SESSION['id']) && $_SESSION['id'] == 3) {
						$save_case[$k] .= create_case_admin($k, $cpt, $val, $link);
						// on augmente le compteur
						$cpt++;
					}
					// utilisateur normal
					else {
						$save_case[$k] .= create_case_key($k, $val, $link);
					}
				}
				else { // cas specifique
					////////////
					// Si c'est rep_id, on sauvegarde tout et on crée tout au moment de n_ana
					if ($k == 'rep_id') {
						$title = $corres[$k];
						$save_case[$k] = create_title($title);
						$save_repid = $k;
						$save_repval = $val;
					}
					else {
						if ($k == 'n_ana') {
							if (isset($_SESSION['id']) && $_SESSION['id'] == 3) {
								// on crée la case rep_id
								$save_case[$save_repid] .= create_case_admin($save_repid, $cpt, $save_repval, $link);
								// on augmente le compteur
								$cpt++;
							}
							// utilisateur normal
							else {
								// on crée la case rep_id
								$save_case[$save_repid] .= create_case_key($save_repid, $save_repval, $link);
							}
							$tab_title_gauche[$k] = add_to_title_gauche($k);
							$ana_temp = create_case($val);
							$save_case[$k] = $ana_temp;
						}
					}
					////////////
				}
				// Si la clé actuelle n'est contenu dans $temp2 (on va afficher uniquement le titre) 
				//~ p($temp2);
				if (!in_array($corres_id_table[$k],$temp2) && $k != 'rep_id') {
					if ($first == true) {
						// on ajoute le titre dans la variable de titre si c'est la premiere ligne
						if ($k == 'n_ana')
							$tabfirst .= $tab_title_gauche[$save_repid];
						$tabfirst .= $tab_title_gauche[$k];
					}
					// on affiche la case
					if ($k == 'n_ana')
							echo $save_case[$save_repid];
					echo $save_case[$k];
				} // Sinon la case est affiché quand on parcours les colonnes normal
			}
		}
		echo '</table>';
		echo '</div>';
		$first = false;
	}
	echo '</div>';
	echo '</div>';
	echo '</div>';
	// On crée le tableau final de la colonne des titres
	return  "<table>".$tabfirst."</table>";
}
?>
