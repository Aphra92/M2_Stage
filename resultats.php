<?php
	session_start();
	//Include
	$title="Résultats";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
if (isset($_GET['id']) &&  isset($_GET['val'])) {
	//~ echo $_GET['id'].'<br>';
	$id = $_GET['id'];
	//~ echo $_GET['val'].'<br>';
	$val = $_GET['val'];
}

if (!isset($request)) {
	//tcr_id
	if 	($id == 'tcr_id') {
		$query = 'select * from tcr.tcr where tcr_id = :val';
		$request = $pdo->prepare($query);
		$request -> bindValue(':val',$val);
		$request -> execute();
	}
	else {
		//p_id
		if ($id == 'p_id') {
			$query = 'select * from tcr.project 
			where p_id = :val
			';
			$request = $pdo->prepare($query);
			$request -> bindValue(':val',$val);
			$request -> execute();
		}
		else {
			//e_id
			if ($id == 'e_id') {
				$val = explode(',',$val);
				$query = 'select * from tcr.experiment 
				where p_id = :p_id
				and e_id = :e_id
				';
				$request = $pdo->prepare($query);
				$request -> bindValue(':p_id',$val[0]);
				$request -> bindValue(':e_id',$val[1]);
				$request -> execute();
			}
			else {
				//o_id	
				if ($id == 'o_id') {
					$val = explode(',',$val);
					$query = 'select * from tcr.organism 
					where p_id = :p_id
					and e_id = :e_id
					and o_id = :o_id
					';
					$request = $pdo->prepare($query);
					$request -> bindValue(':p_id',$val[0]);
					$request -> bindValue(':e_id',$val[1]);
					$request -> bindValue(':o_id',$val[2]);
					$request -> execute();	
				}
				else {
					//s_id	
					if ($id == 's_id') {
						$val = explode(',',$val);
						$query = 'select * from tcr.sample 
						where p_id = :p_id
						and e_id = :e_id
						and o_id = :o_id
						and s_id = :s_id
						';
						$request = $pdo->prepare($query);
						$request -> bindValue(':p_id',$val[0]);
						$request -> bindValue(':e_id',$val[1]);
						$request -> bindValue(':o_id',$val[2]);
						$request -> bindValue(':s_id',$val[3]);
						$request -> execute();
					}
					else {
						//a_id	
						if ($id == 'a_id') {
							$val = explode(',',$val);
							$query = 'select * from tcr.aliquot 
							where p_id = :p_id
							and e_id = :e_id
							and o_id = :o_id
							and s_id = :s_id
							and a_id = :a_id
							';
							$request = $pdo->prepare($query);
							$request -> bindValue(':p_id',$val[0]);
							$request -> bindValue(':e_id',$val[1]);
							$request -> bindValue(':o_id',$val[2]);
							$request -> bindValue(':s_id',$val[3]);
							$request -> bindValue(':a_id',$val[4]);
							$request -> execute();	
						}
						else {
							//m_id	
							if ($id == 'm_id') {
								$val = explode(',',$val);
								$query = 'select * from tcr.manip 
								where p_id = :p_id
								and e_id = :e_id
								and o_id = :o_id
								and s_id = :s_id
								and a_id = :a_id
								and m_id = :m_id
								';
								$request = $pdo->prepare($query);
								$request -> bindValue(':p_id',$val[0]);
								$request -> bindValue(':e_id',$val[1]);
								$request -> bindValue(':o_id',$val[2]);
								$request -> bindValue(':s_id',$val[3]);
								$request -> bindValue(':a_id',$val[4]);
								$request -> bindValue(':m_id',$val[5]);
								$request -> execute();	
							}
							else {
								//run_id	
								if ($id == 'run_id') {
									$query = 'select * from tcr.run 
									where run_id = :val
									';
									$request = $pdo->prepare($query);
									$request -> bindValue(':val',$val);
									$request -> execute();	
								}
								else {
									//rep_id
									if ($id == 'rep_id') {
										$val = explode(',',$val);
										$query = 'select * from tcr.repertoire 
										where p_id = :p_id
										and e_id = :e_id
										and o_id = :o_id
										and s_id = :s_id
										and a_id = :a_id
										and m_id = :m_id
										and run_id = :run_id
										and rep_id = :rep_id 
										and n_ana = :n_ana
										';
										$request = $pdo->prepare($query);
										$request -> bindValue(':p_id',$val[0]);
										$request -> bindValue(':e_id',$val[1]);
										$request -> bindValue(':o_id',$val[2]);
										$request -> bindValue(':s_id',$val[3]);
										$request -> bindValue(':a_id',$val[4]);
										$request -> bindValue(':m_id',$val[5]);
										$request -> bindValue(':run_id',$val[6]);
										$request -> bindValue(':rep_id',$val[7]);
										$request -> bindValue(':n_ana',$val[8]);
										$request -> execute();
									}
									else {
										if ($id == '*') {
											$val = explode(',',$val);
											$query = 'select * from 
												tcr.project,
												tcr.experiment,
												tcr.organism,
												tcr.sample,
												tcr.aliquot,
												tcr.manip,
												tcr.repertoire,
												tcr.run
											where repertoire.p_id = :p_id
											and repertoire.e_id = :e_id
											and repertoire.o_id = :o_id
											and repertoire.s_id = :s_id
											and repertoire.a_id = :a_id
											and repertoire.m_id = :m_id
											and repertoire.run_id = :run_id
											and repertoire.rep_id = :rep_id
											and repertoire.n_ana = :n_ana
											
											and project.p_id = experiment.p_id
											and project.p_id = organism.p_id
											and project.p_id = sample.p_id
											and project.p_id = aliquot.p_id
											and project.p_id = manip.p_id
											and project.p_id = repertoire.p_id
											
											and experiment.e_id = organism.e_id
											and experiment.e_id = sample.e_id
											and experiment.e_id = aliquot.e_id
											and experiment.e_id = manip.e_id
											and experiment.e_id = repertoire.e_id
											
											and organism.o_id = sample.o_id
											and organism.o_id = aliquot.o_id
											and organism.o_id = manip.o_id
											and organism.o_id = repertoire.o_id
											
											and sample.s_id = aliquot.s_id
											and sample.s_id = manip.s_id
											and sample.s_id = repertoire.s_id
											
											and aliquot.a_id = manip.a_id
											and aliquot.a_id = repertoire.a_id
											
											and manip.m_id = repertoire.m_id
											
											and run.run_id = repertoire.run_id
											';
											$request = $pdo->prepare($query);
											$request -> bindValue(':p_id',$val[1]);
											$request -> bindValue(':e_id',$val[2]);
											$request -> bindValue(':o_id',$val[3]);
											$request -> bindValue(':s_id',$val[4]);
											$request -> bindValue(':a_id',$val[5]);
											$request -> bindValue(':m_id',$val[6]);
											$request -> bindValue(':run_id',$val[7]);
											$request -> bindValue(':rep_id',$val[8]);
											$request -> bindValue(':n_ana',$val[9]);
											$request -> execute();
										}
										else {
											if ($id == 'pr_name') {
												$query = 'select * from tcr.protocole
												where pr_name = :val
												';
												$request = $pdo->prepare($query);
												$request -> bindValue(':val',$val);
												$request -> execute();	
											}
											else {
												if ($id == 'st_label') {
													$val = explode(',',$val);
													$query = 'select * from tcr.stat
													where p_id = :p_id
													and e_id = :e_id
													and o_id = :o_id
													and s_id = :s_id
													and a_id = :a_id
													and m_id = :m_id
													and run_id = :run_id
													and rep_id = :rep_id 
													and n_ana = :n_ana
													';
													$request = $pdo->prepare($query);
													$request -> bindValue(':p_id',$val[0]);
													$request -> bindValue(':e_id',$val[1]);
													$request -> bindValue(':o_id',$val[2]);
													$request -> bindValue(':s_id',$val[3]);
													$request -> bindValue(':a_id',$val[4]);
													$request -> bindValue(':m_id',$val[5]);
													$request -> bindValue(':run_id',$val[6]);
													$request -> bindValue(':rep_id',$val[7]);
													$request -> bindValue(':n_ana',$val[8]);
													$request -> execute();	
												}
												else {
													if (($id == 'ac_id')) {
														$val = explode(',',$val);
														if (sizeof($val) == 3) {
															$query = 'select * from tcr.action
															where p_id1 = :p_id
															and e_id1 = :e_id
															and o_id1 = :o_id
															';
															$request = $pdo->prepare($query);
															$request -> bindValue(':p_id',$val[0]);
															$request -> bindValue(':e_id',$val[1]);
															$request -> bindValue(':o_id',$val[2]);
														}
														if (sizeof($val) == 4) {
															$query = 'select * from tcr.action
															where p_id2 = :p_id
															and e_id2 = :e_id
															and o_id2 = :o_id
															and s_id2 = :s_id
															';
															$request = $pdo->prepare($query);
															$request -> bindValue(':p_id',$val[0]);
															$request -> bindValue(':e_id',$val[1]);
															$request -> bindValue(':o_id',$val[2]);
															$request -> bindValue(':s_id',$val[3]);
														}
														if (sizeof($val) == 1) {
															$query = 'select * from tcr.action
															where ac_id = :ac_id
															';
															$request = $pdo->prepare($query);
															$request -> bindValue(':ac_id',$val[0]);
														}
														$request -> execute();	
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
	}
}
if (isset($request)) {
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
		"ac_id" => "",		
	);
	$keys = get_liste_key($pdo, 'tcr');
	//~ p($keys);
	$tcr = array();
	while($row = $request->fetch(PDO::FETCH_ASSOC)) {
		$tcr[] = $row;
	}
	//~ p($tcr);
	if ($id == 'ac_id') 
		echo '<h1 class ="center">Actions</h1>';
	if (sizeof($tcr) == 0)
		echo "Désolé! Aucun résultat n'a été trouvé dans la base de données<br>";
	// Pour tous les cas sauf action et stats
	if ($id != 'st_label') {
		foreach (array_keys($tcr) as $i) {
			$temp = '';
			$save_run = '';
			$save_repid = '';
			$save_repval = '';
			$tempmanip = '';
			echo '<div id ="resultpage">';
			echo '<table class="resultp">'; 
			if ($id != '*') {
				if ($id != 'rep_id' && $id != 'o_id' && $id != 's_id')
					echo '<tr><th></th><th class="res">'.$corres[$id].'</th>';
				else {
					if ($id == 'rep_id') {
						echo '	<tr><th></th><th class="res">'.$corres[$id].' 
								<a href = "resultats.php?id=st_label&amp;val='.$val[0].','.$val[1].','.$val[2].','.$val[3].','.
									$val[4].','.$val[5].','.$val[6].','.$val[7].','.$val[8].'">
										<span class = "link" > (show stats) </span></a>
								</th></tr>';
					}
					else {
						if ($id == 'o_id') {
							echo '	<tr><th></th><th class="res">'.$corres[$id].' 
								<a href = "resultats.php?id=ac_id&amp;val='.$val[0].','.$val[1].','.$val[2].'">
										<span class = "link" > (show actions) </span></a>
								</th></tr>';
						}
						else {
							if ($id == 's_id') {
								echo '	<tr><th></th><th class="res">'.$corres[$id].' 
									<a href = "resultats.php?id=ac_id&amp;val='.$val[0].','.$val[1].','.$val[2].','.$val[3].'">
											<span class = "link" > (show actions) </span></a>
									</th></tr>';
							}
						}
					}
				}
			}
			foreach (array_keys($tcr[$i]) as $k) {
				// si la valeur n'est pas une clé
				if (!in_array($k, $keys)) { 
					// Si c'est run sequencer, on ajoute la sauvegarde du run + la ligne actuelle
					if ($k == 'run_sequencer') {
						echo $save_run;
						echo '<tr><td class = "titlegauche">'.$k.'</td><td>'.$tcr[$i][$k].'</td></tr>';
					}
					else {
						if ($k == 'p_idprev' || $k == 'e_idprev' || $k == 'o_idprev' || $k == 's_idprev' || $k == 'a_idprev' || $k == 'm_idprev') {
							// on sauvegarde au fur et a mesure
							if ($tempmanip == '')
								$tempmanip .= $tcr[$i][$k];
							else 
								$tempmanip .= ','.$tcr[$i][$k];
							// cas normal, on ajoute les deux cases
							if ($k != 'm_idprev') {
								echo '<tr><td class = "titlegauche">'.$k.'</td><td>'.$tcr[$i][$k].'</td></tr>';
							}
							else { // cas final on ajoute les deux cases, un lien avec le tempmanip
								echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id=m_id&amp;val='.$tempmanip.'">
									<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
							}
						}
						else 
							echo '<tr><td class = "titlegauche">'.$k.'</td><td>'.$tcr[$i][$k].'</td></tr>';
					}
				}
				// Si la valeur est une clé c'est un lien
				else { 
					// Si c'est ni tcr_id ni pr_name, on ajoute la valeur a la variable temp pour garder en memoire les clés précédentes
					if ($k != 'tcr_id' && $k != 'pr_name') {
						if ($temp == '')
							$temp .= $tcr[$i][$k];
						else 
							$temp .= ','.$tcr[$i][$k];
					}
					// Si c'est la clé demandé ou n_ana, on affiche sans lien
					if ($k == $id || ($k == 'n_ana' && $id != '*')) {
						echo '<tr><td class = "titlegauche">'.$k.'</td><td>'.$tcr[$i][$k].'</td></tr>';
					}
					// Si on est dans une clé ou on veut ajouter un lien
					else {
						if ($k == 'tcr_id' || $k == 'run_id' || $k == 'pr_name') { // le lien correspond a la valeur normal
							if ($id != '*')
								echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$tcr[$i][$k].'">
								<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
							else { // Si on est dans * le lien de run_id est sauvegardé dans une variable pour l'afficher au niveau de run sequencer
								if ($k == 'run_id') {
									$save_run = 
									'<tr><th></th><th class="res">'.$corres[$k].'</th>
									<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$tcr[$i][$k].'">
									<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
								}
								if ($k == 'pr_name') {
									echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$tcr[$i][$k].'">
									<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
								}
							}
						}
						else {
							// pour tous les autres cas, on affiche un lien avec comme valeur la variable temp qui sauvegarde les valeurs passées
							if ($id != '*') {
								echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$temp.'">
								<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
							}
							else {
								// si c'est rep_id on sauvegarde dans des variables les valeurs a ajouté
								if ($k == 'rep_id') {
									echo '	<tr><th></th><th class="res">'.$corres[$k].' 
												<a href = "resultats.php?id=st_label&amp;val='.$val[1].','.$val[2].','.$val[3].','.$val[4].','.
												$val[5].','.$val[6].','.$val[7].','.$val[8].','.$val[9].'">
												<span class = "link" > (show stats) </span></a>
											</th>';
									$save_repid = $k;
									$save_repval = $tcr[$i][$k];
								}
								else {
									if ($k == 'o_id') {
										echo '	<tr><th></th><th class="res">'.$corres[$k].' 
											<a href = "resultats.php?id=ac_id&amp;val='.$val[1].','.$val[2].','.$val[3].'">
											<span class = "link" > (show actions) </span></a>
										</th>';
										echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$temp.'">
										<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
									}
									else {
										if ($k == 's_id') {
											echo '	<tr><th></th><th class="res">'.$corres[$k].' 
												<a href = "resultats.php?id=ac_id&amp;val='.$val[1].','.$val[2].','.$val[3].','.$val[4].'">
												<span class = "link" > (show actions) </span></a>
											</th>';
											echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$temp.'">
											<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
										}
										else {
											if ($k == 'n_ana') { // on affiche rep_id et n_ana
												echo '<tr><td class = "titlegauche">'.$save_repid.'</td><td><a href = "resultats.php?id='.$save_repid.'&amp;val='.$temp.'">
												<span class = "link" >'.$save_repval.'</span></a></td></tr>';
												echo '<tr><td class = "titlegauche">'.$k.'</td><td>'.$tcr[$i][$k].'</td></tr>';
											}
											else {
												echo '<tr><th></th><th class="res">'.$corres[$k].'</th>';
												echo '<tr><td class = "titlegauche">'.$k.'</td><td><a href = "resultats.php?id='.$k.'&amp;val='.$temp.'">
												<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
											}
										}
									}
								}	
							}
						}
					}
				}
			}
			echo '</table>';
			echo '</div>';
		}
	}
	else {
		$stop = false;
		$temp = '';
		$save_repid = '';
		$save_repval = '';
		$tabkeys = '';
		echo '<div id ="resultpage">';
		foreach (array_keys($tcr) as $i) {
			if (!$stop) {
				echo '<table class="resultp ref">'; 
				echo '<tr><th></th><th class="res ref">References</th>';
			}
			else 
				echo '<table class="resultp">'; 
			foreach (array_keys($tcr[$i]) as $k) {
				// si la valeur n'est pas une clé on l'affiche
				if (!in_array($k, $keys)) { 
					echo '<tr>';
					//~ echo '<td class = "titlegauche">'.$k.'</td>';
					echo '<td>'.$tcr[$i][$k].'</td></tr>';
				}
				// Si la valeur est une clé c'est un lien
				else { 
					// Si on a pas encore affiché les clés, on les sauvegarde
					if (!$stop) {
						if ($temp == '')
							$temp .= $tcr[$i][$k];
						else 
							$temp .= ','.$tcr[$i][$k];
							
						// Si c'est la clé demandé ou n_ana, on affiche sans lien
						if ($k == 'n_ana') {
							echo '<tr>';
							echo '<td class = "titlegauche">'.$save_repid.'</td>';
							echo '<td><a href = "resultats.php?id='.$save_repid.'&amp;val='.$temp.'">
							<span class = "link" >'.$save_repval.'</span></a></td></tr>';
							echo '<tr>';
							echo '<td class = "titlegauche">'.$k.'</td>';
							echo '<td>'.$tcr[$i][$k].'</td></tr>';
						}
						else { //c'est une clé qu'on veut afficher
							if ($k == 'run_id' ) { // le lien correspond a la valeur normal
								echo '<tr>';
								echo '<td class = "titlegauche">'.$k.'</td>';
								echo '<td><a href = "resultats.php?id='.$k.'&amp;val='.$tcr[$i][$k].'">
									<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
							}
							else {
								if ($k == 'rep_id') {
									$save_repid = $k;
									$save_repval = $tcr[$i][$k];
								}
								else {
									if ($k == 'st_label') {
										echo '</table>';
										echo '<table class="resultp">'; 
										echo '<tr><th class="res">'.$tcr[$i][$k].'</th>';
									}
									else {
										echo '<tr>';
										echo '<td class = "titlegauche">'.$k.'</td>';
										echo '<td><a href = "resultats.php?id='.$k.'&amp;val='.$temp.'">
											<span class = "link" >'.$tcr[$i][$k].'</span></a></td></tr>';
									}
								}
							}	
						}
					}
					else {
						if ($k == 'st_label') {
							//~ echo '<th></th>';
							echo '<tr><th class="res">'.$tcr[$i][$k].'</th>';
						}
					}
				}
			}
			echo '</table>';
			$stop = true;
		}
		echo '</div>';
	}
}

?>

</body>
<?php
	include('./includes/footer.php');
?>
</html>
