<?php
include("dblog.php");
include("functions_general.php");
?>

<?php
// fonctions utilisés pour la curation et la modification
// la fonction recupère tout ce qui est entré par l'utilisateur et upate la table correspondant aux clés
// Ces fonctions sont appelé lors d'appel ajax
    if(isset($_POST['term'])) {
        // Mot tapé par l'utilisateur
        $tab = $_POST['term'];
        
        // convert to array
		$tab = json_decode($tab, true);
		
		// check table
		$table = '';
		$req = array();
		foreach (array_keys($tab) as $val) {
			if($table == '') {
				$t1 = explode('|',$val);
				$t2 = array_shift($t1);
				$table = explode(',',$t2);
			}
			$t = explode('|',$val);
			$value = array_pop($t);
			$req[$value] = $tab[$val];
		}
		// Listing des cas possible
		foreach ($table as $val) {
			///
			if ($val == 'project') {
				$query = 'UPDATE tcr.project SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;
				
				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'experiment') {
				$query = 'UPDATE tcr.experiment SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;
				
				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'protocole') {
				$query = 'UPDATE tcr.protocole SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'pr_name')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'pr_name')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;
				
				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'run') {
				$query = 'UPDATE tcr.run SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'run_id')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'run_id')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;
				
				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'stat') {
				$query = 'UPDATE tcr.stat SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id' && $v != 'o_id' && $v != 's_id' && $v != 'a_id' && $v != 'm_id' 
						&& $v != 'run_id' && $v != 'rep_id' && $v != 'n_ana' && $v != 'st_label')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id' || $v == 'o_id' || $v == 's_id' || $v == 'a_id' || $v == 'm_id' 
						|| $v == 'run_id' || $v == 'rep_id' || $v == 'n_ana' || $v == 'st_label')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;
				
				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'tcr') {
				$query = 'UPDATE tcr.tcr SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'tcr_id')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'tcr_id')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;
				
				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			/////
			if ($val == 'organism') {
				$query = 'UPDATE tcr.organism SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id' && $v != 'o_id')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id' || $v == 'o_id')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;

				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					if ($v != 'part_ac') {
						($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
					}
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'action') {
				$query = 'UPDATE tcr.action SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'ac_id' && $v != 'part_ac')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'ac_id')
						$where .= $v.' = :'.$v.' and ';
					if (($v == 'p_id' || $v == 'p_id1') || ($v == 'p_id2' && $req[$v] != ''))
						$nameproj = $req[$v];
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;

				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					if ($v != 'part_ac') {
						($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
					}
					else {
						if ($req[$v] != '') {
							// ajout participe et entity part
							$n = explode('/', $req[$v]);
							foreach ($n as $id) {
								newentity($id, 'tcr', $pdo);
								newpartproj($nameproj, $id, $pdo);
								try {
									$re = 'INSERT INTO tcr.participe_ac (ac_id, part_ac) VALUES (:ac_id, :part_ac);';
									$re = $pdo->prepare($re);
									$re -> bindValue(':ac_id',$req['ac_id'], PDO::PARAM_STR);
									$re -> bindValue(':part_ac',$id, PDO::PARAM_STR);									
									$re -> execute();
								}
								catch (Exception $e){}
							}
						}
					}
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}			
			}
			if ($val == 'manip') {
				$query = 'UPDATE tcr.manip SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id' && $v != 'o_id' && $v != 's_id' && $v != 'a_id' && $v != 'm_id' && $v != 'part_m')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id' || $v == 'o_id' || $v == 's_id' || $v == 'a_id' || $v == 'm_id')
						$where .= $v.' = :'.$v.' and ';
					if ($v == 'p_id')
						$nameproj = $req[$v];
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;

				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					if ($v != 'part_m') {
						($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
					}
					else {
						if ($req[$v] != '') {
							// ajout participe et entity part
							$n = explode('/', $req[$v]);
							foreach ($n as $id) {
								newentity($id, 'tcr', $pdo);
								newpartproj($nameproj, $id, $pdo);
								try {
									$re = 'INSERT INTO tcr.participe_m (p_id, e_id, o_id, s_id, a_id, m_id, part_m) 
									VALUES (:p_id, :e_id, :o_id, :s_id, :a_id, :m_id, :part_m);';
									$re = $pdo->prepare($re);
									$re -> bindValue(':p_id',$req['p_id'], PDO::PARAM_STR);
									$re -> bindValue(':e_id',$req['e_id'], PDO::PARAM_STR);
									$re -> bindValue(':o_id',$req['o_id'], PDO::PARAM_STR);
									$re -> bindValue(':s_id',$req['s_id'], PDO::PARAM_STR);
									$re -> bindValue(':a_id',$req['a_id'], PDO::PARAM_STR);
									$re -> bindValue(':m_id',$req['m_id'], PDO::PARAM_STR);
									$re -> bindValue(':part_m',$id, PDO::PARAM_STR);									
									$re -> execute();
								}
								catch (Exception $e){}
							}
						}
					}
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'sample') {
				$query = 'UPDATE tcr.sample SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id' && $v != 'o_id' && $v != 's_id' && $v != 'part_s')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id' || $v == 'o_id' || $v == 's_id')
						$where .= $v.' = :'.$v.' and ';
					if ($v == 'p_id')
						$nameproj = $req[$v];
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;

				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					if ($v != 'part_s') {
						($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
					}
					else {
						if ($req[$v] != '') {
							// ajout participe et entity part
							$n = explode('/', $req[$v]);
							foreach ($n as $id) {
								newentity($id, 'tcr', $pdo);
								newpartproj($nameproj, $id, $pdo);
								try {
									$re = 'INSERT INTO tcr.participe_s (p_id, e_id, o_id, s_id, part_s) 
									VALUES (:p_id, :e_id, :o_id, :s_id, :part_s);';
									$re = $pdo->prepare($re);
									$re -> bindValue(':p_id',$req['p_id'], PDO::PARAM_STR);
									$re -> bindValue(':e_id',$req['e_id'], PDO::PARAM_STR);
									$re -> bindValue(':o_id',$req['o_id'], PDO::PARAM_STR);
									$re -> bindValue(':s_id',$req['s_id'], PDO::PARAM_STR);
									$re -> bindValue(':part_s',$id, PDO::PARAM_STR);									
									$re -> execute();
								}
								catch (Exception $e){}
							}
						}
					}
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'aliquot') {
				$query = 'UPDATE tcr.aliquot SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id' && $v != 'o_id' && $v != 's_id' && $v != 'a_id' && $v != 'part_a')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id' || $v == 'o_id' || $v == 's_id' || $v == 'a_id')
						$where .= $v.' = :'.$v.' and ';
					if ($v == 'p_id')
						$nameproj = $req[$v];
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;

				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					if ($v != 'part_a') {
						($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
							$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
					}
					else {
						if ($req[$v] != '') {
							// ajout participe et entity part
							$n = explode('/', $req[$v]);
							foreach ($n as $id) {
								newentity($id, 'tcr', $pdo);
								newpartproj($nameproj, $id, $pdo);
								try {
									$re = 'INSERT INTO tcr.participe_a (p_id, e_id, o_id, s_id, a_id, part_a) 
									VALUES (:p_id, :e_id, :o_id, :s_id, :a_id, :part_a);';
									$re = $pdo->prepare($re);
									$re -> bindValue(':p_id',$req['p_id'], PDO::PARAM_STR);
									$re -> bindValue(':e_id',$req['e_id'], PDO::PARAM_STR);
									$re -> bindValue(':o_id',$req['o_id'], PDO::PARAM_STR);
									$re -> bindValue(':s_id',$req['s_id'], PDO::PARAM_STR);
									$re -> bindValue(':a_id',$req['a_id'], PDO::PARAM_STR);
									$re -> bindValue(':part_a',$id, PDO::PARAM_STR);									
									$re -> execute();
								}
								catch (Exception $e){}
							}
						}
					}
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
			if ($val == 'repertoire') {
				$query = 'UPDATE tcr.repertoire SET ';
				$where = 'WHERE ';
				foreach (array_keys($req) as $v) {
					// si on est pas une clé ni un part_
					if ($v != 'p_id' && $v != 'e_id' && $v != 'o_id' && $v != 's_id' && $v != 'a_id' && $v != 'm_id' 
						&& $v != 'run_id' && $v != 'rep_id' && $v != 'n_ana')
						$query .= $v.' = :'.$v.' , ';
					if ($v == 'p_id' || $v == 'e_id' || $v == 'o_id' || $v == 's_id' || $v == 'a_id' || $v == 'm_id' 
						|| $v == 'run_id' || $v == 'rep_id' || $v == 'n_ana')
						$where .= $v.' = :'.$v.' and ';
				}
				$query = substr($query,0,-2);
				$where = substr($where,0,-5);
				$query = $query.$where;

				$request = $pdo->prepare($query);
				foreach (array_keys($req) as $v) {
					($req[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
						$request -> bindValue(':'.$v,$req[$v], PDO::PARAM_STR));
				}
				try {
					$request -> execute();
				}
				catch (Exception $e) {
					echo '{"Something went wrong":"data"}';
				}
			}
		}
	}
?>
