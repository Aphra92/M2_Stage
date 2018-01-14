<?php
// Print value with <br>
function p($val) {
    echo "<br>";
    print_r($val);
    echo "<br>";
}

// function to get the table_name of a schema
function get_table_name($pdo, $schema) {
	$query = 'SELECT table_name FROM information_schema.tables WHERE table_schema = :schema'; // prepare request
	$request = $pdo->prepare($query);
	$request -> bindValue(':schema',$schema, PDO::PARAM_STR); // bind value $schema to :schema
	$request -> execute();	//execute request
	
	$table_name = array();
	foreach ($request as $row) {
		$table_name[] = $row['table_name'];
	}
	return $table_name;
}

// function to get the columns names of a table
function get_columns_names($pdo, $schema, $table) {
	$query = 'SELECT table_name , column_name FROM information_schema.columns WHERE table_schema = :schema AND table_name = :table';
	$request = $pdo->prepare($query);
	$request -> bindValue(':schema',$schema, PDO::PARAM_STR); // bind value $schema to :schema
	$request -> bindValue(':table',$table, PDO::PARAM_STR);
	$request -> execute();	//execute request
	
	$columns_names = array();
	while($row = $request->fetch(PDO::FETCH_ASSOC)) {
		// On ajoute les données dans un tableau
		$columns_names[$row['table_name']][] = $row['column_name'];
    }
    //~ p($columns_names);
	return $columns_names;
}

function get_columns($pdo, $schema, $table) {
	$query = 'SELECT column_name FROM information_schema.columns WHERE table_schema = :schema AND table_name = :table'; // prepare request
	$request = $pdo->prepare($query);
	$request -> bindValue(':schema',$schema, PDO::PARAM_STR); // bind value $schema to :schema
	$request -> bindValue(':table',$table, PDO::PARAM_STR);
	$request -> execute();	//execute request
	
	$columns_names = array();
	foreach ($request as $row) {
		$columns_names[$row['column_name']] = $table;
	}
	return $columns_names;
}

// recupere les informations sur les contraintes pour chaque colonne
function get_info_columns($pdo, $schema){
	$query = ' 	SELECT column_name , data_type, character_maximum_length
				FROM INFORMATION_SCHEMA.COLUMNS where table_schema = :schema';
	$request = $pdo->prepare($query);
	$request -> bindValue(':schema',$schema, PDO::PARAM_STR);
	$request -> execute();
	
	$columns_infos = array();
	foreach ($request as $row) {
		$len = $row['character_maximum_length'];
		$type = $row['data_type'];
		if ($len == '')
			$len = 'null';
		if ($type == 'integer') {
			$type = 'int';
		}
		if ($type == 'double precision') {
			$type = 'double';
		}
		else {
			if ($type == 'character varying') 
				$type = 'text';
		}
		$columns_infos[$row['column_name']] = $type.','.$len;
	}
	return $columns_infos;
}

// fonction pour concatener un tableau a un tableau
function concatarray($form, $columns_names) {
	foreach (array_keys($columns_names) as $key){
		if (!array_key_exists($key, $form))
			$form[$key] = $columns_names[$key];
		else { // on concatene la valeur
			$form[$key] .= ','.$columns_names[$key];
		}
	}
	return $form;
}

// fonction pour ajouter les participants au formulaire
function add_participant($val, $columns_names, $schema, $pdo) {
	//~ echo $val;
	if ($val == 'Project') {
		// les participants sont ajoutés dans les autres cas
		return $columns_names;
	}
	if ($val == 'Experiment') {
		// pas de participants
		return $columns_names;
	}
	if ($val == 'Organism') {
		// pas de participants
		return $columns_names;
	}
	if ($val == 'Protocole') {
		// pas de participants
		return $columns_names;
	}
	if ($val == 'Sample') {
		$str = 'participe_s';
		$col = get_columns($pdo, $schema, $str);
		$columns_names = concatarray($columns_names, $col);
		return $columns_names;
	}
	if ($val == 'Aliquot') {
		$str = 'participe_a';
		$col = get_columns($pdo, $schema, $str);
		$columns_names = concatarray($columns_names, $col);
		return $columns_names;
	}
	if ($val == 'Action') {
		$str = 'participe_ac';
		$col = get_columns($pdo, $schema, $str);
		$columns_names = concatarray($columns_names, $col);
		return $columns_names;
	}
	if ($val == 'Manip') {
		$str = 'participe_m';
		$col = get_columns($pdo, $schema, $str);
		$columns_names = concatarray($columns_names, $col);
		return $columns_names;
	}
	if ($val == 'Run') {
		return $columns_names;
	}
	if ($val == 'Repertoire') {
		return $columns_names;
	}
	if ($val == 'Stat') {
		return $columns_names;
	}
	if ($val == 'TCR') {
		return $columns_names;
	}
}

// function to get the constraints and names of columns
function get_columns_names_constraints($pdo, $schema) {
	$query = 'SELECT  distinct(i.column_name),c.conname, c.contype FROM information_schema.constraint_column_usage as i , 
				pg_constraint as c WHERE i.table_schema = :schema AND c.conname = i.constraint_name'; // prepare request
	$request = $pdo->prepare($query);
	$request -> bindValue(':schema',$schema, PDO::PARAM_STR); // bind value $schema to :schema
	$request -> execute();	//execute request
	
	$columns_names_constraints = array();
	foreach ($request as $row) {
		$temp = array();
		$temp[] = $row['column_name'];
		$temp[] = $row['conname'];
		$temp[] = $row['contype'];
		$columns_names_constraints[] = $temp;
	}
	return $columns_names_constraints;
}

// fonction pour recuperer la liste des contraintes dans un schema
function get_liste_key($pdo, $schema) {
	$columns_names_constraints = get_columns_names_constraints($pdo, $schema);
	$temp = array();
	foreach ($columns_names_constraints as $val) {
		if (($val[2] == 'p' || $val[2] == 'f') && (!in_array($val[0], $temp))){
			if ($val[0] != 'identifier') {
				array_push($temp,$val[0]);
			}
			//~ else {
				//~ array_push($temp,$val[1]);
			//~ }
		}	
	}
	return $temp;
}

// affiche un formulaire a l ecran
function create_form($form, $countrow, $columns_infos, $pdo, $foreign, $auto, $opt, $hide, $newkey, $verr, $choice, $save) {	
	// Create form
	echo '<div id = "tab">';
	echo '<div id = "title">';
	foreach (array_keys($form) as $f){
		// Affiche les colonnes qui ne sont pas dans hide
		if (!in_array($f, $hide))
			//~ echo '<TD>'.$f.'</TD>';
			echo '<input type="text" value = '.$f.' disabled="disabled">';
	}
	//~ echo ($choice == TRUE ? '<TD>Curation<input type = "button" name = "" value = "confirm" style="visibility: hidden;"></TD>' : '');
	echo ($choice == TRUE ? '<input type="text" value ="Curation" disabled="disabled">
							<input type = "button"  value = "confirm" style="visibility: hidden;">' : '');
	echo '</div>';
	echo '<form action="./form_conf.php" method="post">';
	echo '<div id = "corp">';
	// Affiche autant de ligne que demander
	for ($i = 0; $i < $countrow; $i++) {
		echo '<div id = "line_'.$i.'" class = "line">';
		// Affiche les colonnes qui ne sont pas dans hide
		foreach (array_keys($form) as $f) {
			if (!in_array($f, $hide)){
				$colinf = explode(',', $columns_infos[$f]);
				$colinfshift = array_shift($colinf);
				$colinfpop = array_pop($colinf);
				
				$match = explode('_', $f);
				$matchshift = array_shift($match);
				$matchpop = array_pop($match);
				
				// Create form with default pattern
				echo '<input 	type="text" 
								name="'.$form[$f].'|'.$i.'|'.$f.'" 
								title = ""
								class = "elem_'.$i.'"'.
								(in_array($f, $opt) == TRUE ? '': ' required ').
								
								(preg_match("/text/", $colinfshift) == 1 ? 
								' maxlength="'.$colinfpop.'"': '').
								
								(in_array($f, $verr) == 1 ? 
								' readonly="readonly" ': '').
								
								(preg_match("/resp/",$matchshift) == 1 ?
								' pattern="[^/]+"' : '').
								
								(preg_match("/curation/",$matchpop) == 1 ?
								' pattern="[Oo][Kk]|[Pp][Aa][Rr][Tt][Ii][Aa][Ll]|[Nn][Oo]"' : '').
								
								(preg_match("/part/",$matchshift) == 1 ?
								' pattern="[^-.,;:+%]+"' : '').
								
								(preg_match("/date/", $colinfshift) == 1 ? 
								' pattern="(?:19|20)[0-9]{2}(-|/)(?:(?:0[1-9]|1[0-2])(-|/)(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])(-|/)(?:30))|(?:(?:0[13578]|1[02])(-|/)31))"': '').
									
								(preg_match("/double/", $colinfshift) == 1 ? 
								' pattern="(\d*)|(\d*\.\d*)$"': '').
								
								(preg_match("/int/", $colinfshift) == 1 ? 
								' pattern="(\d*)$"': '').
								
								(preg_match("/boolean/", $colinfshift) == 1 ? 
								' pattern="[Tt][Rr][Uu][Ee]|[Ff][Aa][Ll][Ss][Ee]"': '').
								
								/*'onClick="check(\'line_'.$i.'\')"'.*/
								'>';
				
			}
		}
		echo ($choice == TRUE ? '<div class = "button"><input type = "button" name = "'.$i.'" 
		value = "validation" onClick="validate(\''.$i.'\');"></div>' : '');
		echo '</div>';
	}
	echo '</div>';
	echo '<input type="text" name="save" value = "'.$save.'" class = "save" style="visibility: hidden;">';
	echo ($choice == FALSE ? '<input type="submit" value="submit" class="data.send" style="visibility: hidden;"></form></div><br>' : '</form></div><br>');

	check_attribution_colonnes($pdo, $form, $countrow, $foreign, $auto, $newkey, $choice);
}

/* fonction pour la recherche */
function create_request_tab($nametable, $table, $row, $val, $name) {
	if (!array_key_exists($nametable, $table[$row])) {
				$table[$row][$nametable] = array();
				$table[$row][$nametable][$name] = (string) $val;
	}
	else {
				$table[$row][$nametable][$name] = (string) $val;
	}
	return $table;
} 

function config_form($POST, $pdo, $schema, $choice) {
	// Initialize les array
	$form = array();
	$foreign = array();
	$auto = array();
	$opt = array();
	$hide = array();
	$newkey = array();
	$verr = array();
	//$nbtab = 0;
	
	/*if (array_key_exists('Sample', $POST)){
		$POST['Action'] = 'Action';
	}*/
	//~ p($POST);
		
	// Pour toutes les cles dans $_POST sauf quantity
	// (On parcours la liste des tables)
	foreach (array_keys($POST) as $val) {
		if ($val == 'table')
			$val = $POST[$val];
		//~ echo $val;
		// Creation des tableaux pour chaque cas possible
		if ($val == 'Manip') {
			$foreigntemp = array(
				"pr_name"
			);
			$newkeytemp = array();
			$autotemp = array(
				"m_output",
				"m_input",
				"m_type",
				"m_idprev",
				"part_m"
			);
			$opttemp = array(
				"m_comment",
				"m_ref_comment",
				"m_label",
				"m_tag",
				"m_val1",
				"m_val2",
				"m_val3",
				"m_val4",
				"m_val5",
				"m_val6",
				"m_val7",
				"m_val8",
				"m_idprev",
				"m_excluded"
			);
			$verrtemp = array(
				"p_idprev",
				"e_idprev",
				"o_idprev",
				"s_idprev",
				"a_idprev",
			);
			$hidetemp = array();
			
			// Distinction ajout / curration
			if ($choice == 0) {
				$foreigntemp[] = "p_id";
				$foreigntemp[] = "e_id";
				$foreigntemp[] = "o_id";
				$foreigntemp[] = "s_id";
				$foreigntemp[] = "a_id";
				
				$hidetemp[] = "m_curation";
				$hidetemp[] = "m_curation_date";
				$hidetemp[] = "m_curation_id";
				
				$newkeytemp[] = "m_id";
				
				$autotemp[] = "resp_manip";
			}
			else {
				$autotemp[] = "m_curation";
				$autotemp[] = "m_curation_id";
				
				$verrtemp[] = "p_id";
				$verrtemp[] = "e_id";
				$verrtemp[] = "o_id";
				$verrtemp[] = "s_id";
				$verrtemp[] = "a_id";
				$verrtemp[] = "m_id";
				$verrtemp[] = "resp_manip";
			}
		}
		else {
			if ($val == 'Action') {
				$foreigntemp = array(
				);
				$newkeytemp = array();
				$autotemp = array(
					"ac_type",
					"ac_unit",
					"part_ac"
				);
				$opttemp = array(
					"m_comment",
					"ac_unit",
					"ac_val",
					"ac_treat_id",
					"ac_time",
					"ac_comment",
					"ac_ref_comment",
				);
				$verrtemp = array();
				$hidetemp = array();
				
				// Distinction ajout / curration
				if ($choice == 0) {
					$foreign[] = "p_id1";
					$foreign[] = "e_id1";
					$foreign[] = "o_id1";
					$foreign[] = "p_id2";
					$foreign[] = "e_id2";
					$foreign[] = "o_id2";
					$foreign[] = "s_id2";
					$hidetemp[] = "ac_curation";
					$hidetemp[] = "ac_curation_date";
					$hidetemp[] = "ac_curation_id";
					
					$autotemp[] = "resp_action";

					$newkeytemp[] = "ac_id";
				}
				else {
					$autotemp[] = "ac_curation";
					$autotemp[] = "ac_curation_id";
					
					$verrtemp[] = "resp_action";
					$verrtemp[] = "ac_id";
					$verrtemp[] = "p_id1";
					$verrtemp[] = "e_id1";
					$verrtemp[] = "o_id1";
					$verrtemp[] = "p_id2";
					$verrtemp[] = "e_id2";
					$verrtemp[] = "o_id2";
					$verrtemp[] = "s_id2";
				}
			}
			else {
				if ($val == 'Protocole') {
					$foreigntemp = array();
					$newkeytemp = array();
					$autotemp = array(
						"pr_type_tag",
						"pr_val1_lab",
						"pr_val1_unit",
						"pr_val2_lab",
						"pr_val2_unit",
						"pr_val3_lab",
						"pr_val3_unit",
						"pr_val4_lab",
						"pr_val4_unit",
						"pr_val5_lab",
						"pr_val5_unit",
						"pr_val6_lab",
						"pr_val6_unit",
						"pr_val7_lab",
						"pr_val7_unit",
						"pr_val8_lab",
						"pr_val8_unit",
					);
					$opttemp = array(
						"pr_type_tag",
						"pr_val1_lab",
						"pr_val1_unit",
						"pr_val2_lab",
						"pr_val2_unit",
						"pr_val3_lab",
						"pr_val3_unit",
						"pr_val4_lab",
						"pr_val4_unit",
						"pr_val5_lab",
						"pr_val5_unit",
						"pr_val6_lab",
						"pr_val6_unit",
						"pr_val7_lab",
						"pr_val7_unit",
						"pr_val8_lab",
						"pr_val8_unit",
					);
					$verrtemp = array();
					$hidetemp = array();
					// Distinction ajout / curration
					if ($choice == 0) {
						$newkeytemp[] = "pr_name";
					}
					else { // modif
						$verrtemp[] = "pr_name";
					}
				}
				else {
					if ($val == 'Run') {
						$foreigntemp = array();
						$newkeytemp = array();
						$autotemp = array(
							"run_sequencer",
							"run_seq_method",
							"run_seq_direction",
							"run_seq_chemistry",
						);
						$opttemp = array(
							"run_seq_chemistry",
							"run_seq_lenght",
							"run_comment",
							"run_ref_comment"
						);
						$verrtemp = array();
						$hidetemp = array();
						// Distinction ajout / curration
						if ($choice == 0) {
							$newkeytemp[] = "run_id";
							$autotemp[] = "resp_run";
						}
						else { // modif
							$verrtemp[] = "run_id";
							$verrtemp[] = "resp_run";

						}
					}
					else {
						if ($val == 'Project') {
							$foreigntemp = array();
							$newkeytemp = array();
							$autotemp = array();
							$opttemp = array(
								"p_comment",
								"p_end"
							);
							$verrtemp = array();
							$hidetemp = array();
							// Distinction ajout / curration
							if ($choice == 0) {
								$newkeytemp[] = "p_id";
								$autotemp[] = "resp_project";
							}
							else { // modif
								$verrtemp[] = "p_id";
								$verrtemp[] = "resp_project";
							}
						}
						else {
							if ($val == 'Experiment') {
								$foreigntemp = array();
								$newkeytemp = array();
								$autotemp = array(
								);
								$opttemp = array(
									"e_comment"
								);
								$verrtemp = array();
								$hidetemp = array();
								if ($choice == 0) {
									$foreigntemp[] = "p_id";
									$newkeytemp[] = "e_id";
								}
								else { // modif
									$verrtemp[] = "p_id";
									$verrtemp[] = "e_id";
								}
							}
							else {
								if ($val == 'Organism') {
									$foreigntemp = array();
									$newkeytemp = array();
									$autotemp = array(
										"o_sex",
										"o_age_1",
										"o_age_2",
										"o_species",
										"o_strain",
										"o_exp_group1",
										"o_exp_group2",
									);
									$opttemp = array(
										"o_comment",
									);
									$verrtemp = array();
									$hidetemp = array();
									
									// Distinction ajout / curration
									if ($choice == 0) {
										$hidetemp[] = "o_curation";
										$hidetemp[] = "o_curation_date";
										$hidetemp[] = "o_curation_id";
										
										$foreigntemp[] = "p_id";
										$foreigntemp[] = "e_id";
										
										$newkeytemp[] = "o_id";
									}
									else {
										$autotemp[] = "o_curation";
										$autotemp[] = "o_curation_id";
										
										$verrtemp[] = "p_id";
										$verrtemp[] = "e_id";
										$verrtemp[] = "o_id";
									}
								}
								else {
									if ($val == 'Sample') {
										$foreigntemp = array();
										$newkeytemp = array();
										$autotemp = array(
											"s_organ",
											"s_cell",
											"s_sort",
											"s_de",
											"s_exp_group2",
											"part_s",
											"s_exp_group1",
										);
										$opttemp = array(
											"s_comment",
											"s_ref_comment",
										);
										$verrtemp = array(
											"s_cell_sample",
										);
										$hidetemp = array();
										
										// Distinction ajout / curration
										if ($choice == 0) {
											$foreigntemp[] = "p_id";
											$foreigntemp[] = "e_id";
											$foreigntemp[] = "o_id";
											
											$newkeytemp[] = "s_id";
											
											$hidetemp[] = "s_curation";
											$hidetemp[] = "s_curation_date";
											$hidetemp[] = "s_curation_id";
											
											$autotemp[] = "resp_sample";
										}
										else {
											$autotemp[] = "s_curation";
											$autotemp[] = "s_curation_id";
											
											$verrtemp[] = "p_id";
											$verrtemp[] = "e_id";
											$verrtemp[] = "o_id";
											$verrtemp[] = "s_id";
											$verrtemp[] = "resp_sample";
										}
									}
									else {
										if ($val == 'Aliquot') {
											$foreigntemp = array();
											$newkeytemp = array();
											$autotemp = array(
												"a_type",
												"a_storage",
												"part_a"
											);
											$opttemp = array(
												"a_comment",
												"a_ref_comment"
											);
											$verrtemp = array();
											$hidetemp = array();
											
											// Distinction ajout / curration
											if ($choice == 0) {
												$hidetemp[] = "a_curation";
												$hidetemp[] = "a_curation_date";
												$hidetemp[] = "a_curation_id";
												
												$foreigntemp[] = "p_id";
												$foreigntemp[] = "e_id";
												$foreigntemp[] = "o_id";
												$foreigntemp[] = "s_id";
												
												$newkeytemp[] = "a_id";
												$autotemp[] = "resp_aliquot";
											}
											else {
												$autotemp[] = "a_curation";
												$autotemp[] = "a_curation_id";
												
												$verrtemp[] = "p_id";
												$verrtemp[] = "e_id";
												$verrtemp[] = "o_id";
												$verrtemp[] = "s_id";
												$verrtemp[] = "a_id";
												$verrtemp[] = "resp_aliquot";
											}
										}
										else {
											if ($val == 'Repertoire') {
												$foreigntemp = array();
												$newkeytemp = array();
												$autotemp = array(
													"rep_meth_id_tcr",
													"rep_version_id_tcr",
													"rep_base_reference",
													"rep_type_workflow",
													"rep_version_workflow",
												);
												$opttemp = array(
													"rep_comment",
													"carac_comment",
												);
												$verrtemp = array();
												$hidetemp = array();
												
												// Distinction ajout / curration
												if ($choice == 0) {
													$foreigntemp[] = "p_id";
													$foreigntemp[] = "e_id";
													$foreigntemp[] = "o_id";
													$foreigntemp[] = "s_id";
													$foreigntemp[] = "a_id";
													$foreigntemp[] = "m_id";
													$foreigntemp[] = "run_id";
													
													$hidetemp[] = "rep_curation";
													$hidetemp[] = "rep_curation_id";
													$hidetemp[] = "rep_curation_date";
													
													$newkeytemp[] = "n_ana";
													$newkeytemp[] = "rep_id";
												}
												else {
													$autotemp[] = "rep_curation";
													$autotemp[] = "rep_curation_id";
													
													$verrtemp[] = "p_id";
													$verrtemp[] = "e_id";
													$verrtemp[] = "o_id";
													$verrtemp[] = "s_id";
													$verrtemp[] = "a_id";
													$verrtemp[] = "a_id";
													$verrtemp[] = "m_id";
													$verrtemp[] = "run_id";
													$verrtemp[] = "n_ana";
													$verrtemp[] = "rep_id";
												}
											}
											else {
												if ($val == 'Stat') {
													$foreigntemp = array();
													$newkeytemp = array();
													$autotemp = array();
													$opttemp = array();
													$verrtemp = array(
														"p_id",
														"e_id",
														"o_id",
														"s_id",
														"a_id",
														"m_id",
														"run_id",
														"rep_id",
														"n_ana",
														"st_label",
													);
													$hidetemp = array();
												}
												else {
													if ($val == 'TCR') {
														$foreigntemp = array();
														$newkeytemp = array();
														$autotemp = array();
														$opttemp = array();
														$verrtemp = array(
															"tcr_id",
														);
														$hidetemp = array();
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		if ($val != 'quantity' && $val != 'select') {
			$auto = array_merge($auto, $autotemp);
			$opt = array_merge($opt, $opttemp);
			$hide = array_merge($hide, $hidetemp);
			$verr = array_merge($verr, $verrtemp);
			
			$newkey = array_merge($newkey, $newkeytemp);
			$foreign = array_merge($foreign, $foreigntemp);	
			
			/*if ($nbtab > 0) {
				$foreign = array_diff($foreign,$newkey);
				$foreign = array_unique($foreign);
			}
			$nbtab += 1;*/			
			
			$str = strtolower($val);	// converti en minuscule pour pouvoir requete
			$table = ''.$str.'';		// nom de la table
			// recupere l'ensemble des colonnes de la table en cle et stock en valeur le nom de la table
			// ex : [projid] => project [p_start] => project
			$columns_names = get_columns($pdo, $schema, $table);
			//~ p($columns_names);
			// Ajoute les colonnes des tables de participation au formulaire
			// la colonne identifier est renomme en 'co_worker_identifier_nomtable'
			// Suppression des doublons au fur et a mesure
			$columns_names = add_participant($val, $columns_names, $schema, $pdo);
			//~ p($columns_names);
			// ajoute les colonnes de la table en cours au tableau final
			$form = concatarray($form, $columns_names);
		}
	}
	$return = array(
			"foreign" => $foreign,
			"auto" => $auto,
			"opt" => $opt,
			"newkey" => $newkey,
			"verr" => $verr,
			"form" => $form,
			"hide" => $hide,
		);
	return $return;
}

function newentity($val, $schema, $pdo) {
	try {
		$addentity = 'INSERT INTO '.$schema.'.entity (identifier) VALUES (:val)';
		$addentity = $pdo->prepare($addentity);
		($val == '' ? $addentity -> bindValue(':val','unknown', PDO::PARAM_STR) : 
						$addentity -> bindValue(':val',$val, PDO::PARAM_STR));
		$addentity -> execute();
	}
	catch (Exception $e){
		//~ echo '<br> Entity '.$val.' already in the db';
	}
}

function newpartproj($proj, $val, $pdo){
	try {
		$add = 'INSERT INTO tcr.participe_proj (p_id, part_proj) VALUES (:pid, :val)';
		//~ echo $add;
		$add = $pdo->prepare($add);
		$add -> bindValue(':pid',$proj, PDO::PARAM_STR);
		$add -> bindValue(':val',$val, PDO::PARAM_STR);
		$add -> execute();
	}
	catch (Exception $e) {
		//echo '<br> Partproj alredy exist';
	}
}
?>
