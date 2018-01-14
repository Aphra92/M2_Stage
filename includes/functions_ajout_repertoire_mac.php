<?php

// Fonction pour check si les fichiers existe avant de lancer la création du repertoire
// c'est exactement la meme chose que le debut d'after repertoire
// Potentiellement a améliorer (il faudrait return un array pour l'autre fonction)
function checkfiles ($tab) {
	$serveuradresse = '/Users/Hadrien/Desktop/Contrebasse/Raw data';
	foreach ($tab as $newrep) {
		$next = false;
		$rep = $newrep['repertoire'];
		// on recupère l'emplacement théorique
		$filepath = $serveuradresse.$rep['rep_location'];
		// A SUPPRIMER
		if ($rep['run_id'] == 'tes')
			$filepath = $serveuradresse.'/RepSeq/RS_Data/tes/tes/TSV/collapsed/P1_01_1_QC.tsv';
		if ($rep['run_id'] == 'test')
			$filepath = $serveuradresse.'/RepSeq/RS_Data/test/test/TSV/collapsed/P1_01_1_QC.tsv';
		////////
		$cl = explode('_QC.tsv',$filepath);
		$cl = $cl[0];
		$cl .= '_CLdb.txt';
		if (file_exists($filepath)) {
			//  chemin théorique pour le run_inf
			$fileruninf = explode('TSV/',$filepath);
			$fileruninf = $fileruninf[0].'run_inf';
			if (file_exists($fileruninf)) {
				$name = explode('/',$filepath);
				$name = $name[sizeof($name)-1];
				$name = substr($name,0, -4);
				$fileruninf = $fileruninf.'/'.$name.'/';
				if (file_exists($fileruninf)) {
					if (file_exists($fileruninf.'/'.$name.'_seq_summary.txt'))
						$seqsumm = $fileruninf.'/'.$name.'_seq_summary.txt';
					if (file_exists($fileruninf.'/'.$name.'_summary.txt'))
						$seq = $fileruninf.'/'.$name.'_summary.txt';
					if (file_exists($cl))
						$cldb = $cl;
					if (isset($seqsumm) && isset($seq) && isset($cldb))
						$next = true;
				}
			}
		}
	return $next;
	}
}
function afterrepertoire($tab, $pdo) {
	$serveuradresse = '/Users/Hadrien/Desktop/Contrebasse/Raw data';
	
	echo '<br><br><br><br> ######################## <br><br><br><br>';
	foreach ($tab as $newrep) {
		$next = false;
		p($newrep);
		$rep = $newrep['repertoire'];
		// on recupère l'emplacement théorique
		$filepath = $serveuradresse.$rep['rep_location'];
		// A SUPPRIMER
		if ($rep['run_id'] == 'tes')
			$filepath = $serveuradresse.'/RepSeq/RS_Data/tes/tes/TSV/collapsed/P1_01_1_QC.tsv';
		if ($rep['run_id'] == 'test')
			$filepath = $serveuradresse.'/RepSeq/RS_Data/test/test/TSV/collapsed/P1_01_1_QC.tsv';
		////////
		$cl = explode('_QC.tsv',$filepath);
		$cl = $cl[0];
		$cl .= '_CLdb.txt';
		if (file_exists($filepath)) {
			//  chemin théorique pour le run_inf
			$fileruninf = explode('TSV/',$filepath);
			$fileruninf = $fileruninf[0].'run_inf';
			if (file_exists($fileruninf)) {
				$name = explode('/',$filepath);
				$name = $name[sizeof($name)-1];
				$name = substr($name,0, -4);
				$fileruninf = $fileruninf.'/'.$name.'/';
				if (file_exists($fileruninf)) {
					if (file_exists($fileruninf.'/'.$name.'_seq_summary.txt'))
						$seqsumm = $fileruninf.'/'.$name.'_seq_summary.txt';
					if (file_exists($fileruninf.'/'.$name.'_summary.txt'))
						$seq = $fileruninf.'/'.$name.'_summary.txt';
					if (file_exists($cl))
						$cldb = $cl;
					if (isset($seqsumm) && isset($seq) && isset($cldb))
						$next = true;
				}
			}
		}
		if ($next) {
			/* on a le fichier contenant les séquences dans $filepath
			et les stats dans $seq et $seqsum */
			echo '<br>';
			echo $filepath.'<br>';
			echo $seq.'<br>';
			echo $seqsumm.'<br>';
			
			// recuperation des clés grace au tableau
			$p = $rep['p_id'];
			$e = $rep['e_id'];
			$o = $rep['o_id'];
			$s = $rep['s_id'];
			$a = $rep['a_id'];
			$m = $rep['m_id'];
			$runid = $rep['run_id'];
			$repid = $rep['rep_id'];
			$nana = $rep['n_ana'];
			$keys = array(
				'p' => $p,
				'e' => $e,
				'o' => $o,
				's' => $s,
				'a' => $a,
				'm' => $m,
				'runid' => $runid,
				'repid' => $repid,
				'nana' => $nana
			);
			
			// Ajout des stats depuis _seq_summary
			$file = fopen($seqsumm,"r");
			if ($file) {
				$i = 0;
				while(($buffer = fgets($file, 4096)) !== false) {
					//~ echo $buffer.'<br>';
					$date = date("Y-m-d",filemtime($seqsumm));
					addstat($buffer, TRUE, $date, $i, $keys, $pdo);
					$i++;
				}
				if (!feof($file)) {
					echo "error\n<br>";
				}
				fclose($file);
				//~ echo $i;
			}
			// Ajout des stats depuis _summary
			$file = fopen($seq,"r");
			if ($file) {
				$i = 0;
				while(($buffer = fgets($file, 4096)) !== false) {
					//~ echo $buffer.'<br>';
					$date = date("Y-m-d",filemtime($seq));
					if ($i == 0) {
						$label = explode("\t",$buffer);
					}
					if ($i ==1) {
						$val = explode("\t",$buffer);
						$j = 1;
						foreach ($label as $l) {
							try {
								//~ echo $l;
								//~ echo $val[$j];
								$query = "INSERT INTO tcr.stat VALUES (:p,:e,:o,:s,:a,:m,:runid,:repid,:nana,:stlabel,:stvalue,
								:stunit,:stcomment)";
								$pdo->beginTransaction();	
								$request = $pdo->prepare($query);
								
								($val[$j] == '' ? $request -> bindValue(':stvalue',null, PDO::PARAM_STR) : 
											$request -> bindValue(':stvalue',$val[$j], PDO::PARAM_STR));
								($l == '' ? $request -> bindValue(':stlabel',null, PDO::PARAM_STR) : 
											$request -> bindValue(':stlabel',$l, PDO::PARAM_STR));
								$request -> bindValue(':stunit','N/A', PDO::PARAM_STR);
								$request -> bindValue(':stcomment',$date, PDO::PARAM_STR);
								
								foreach(array_keys($keys) as $v) {
									//~ echo $v;
									//~ echo $keys[$v];
									($keys[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
												$request -> bindValue(':'.$v,$keys[$v], PDO::PARAM_STR));
								}
								$request -> execute();
								//~ echo '<br>requete effectué<br>';
								$pdo->commit();
							}
							catch (Exception $ex) { 
								$pdo->rollBack();
							}
							$j++;
						}
					}
					$i++;
				}
				if (!feof($file)) {
					echo "error\n<br>";
				}
				fclose($file);
				//~ echo $i;
			}
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// Ajout des tcr
			////////// Attention il faut lire le fichier CLDB pas TSV 
			// Ajouter verification de sa presence au debut
			$file = fopen($cldb,"r");
			if ($file) {
				$i = 0;
				// recupere le tcr max dans la base
				$query = "SELECT MAX(tcr_id) FROM tcr.tcr;";	
				$request = $pdo->prepare($query);
				$request -> execute();
				$aswr = $request->fetch();
				$newpktcr = $aswr['max']+1;
				if ($newpktcr == null)
					$newpktcr = 1;
						
				// On lit le fichier ligne par ligne
				while(($buffer = fgets($file, 4096)) !== false) {
					if ($i != 0) {
						// Pour chaque ligne 
						$line = explode("\t",$buffer);
						//~ p($line);
						//~ echo '<br>';
						$chaintemp = str_split ($line[1]);
						$chain = $chaintemp[2];
						$vlabel = $line[1];
						$jlabel = $line[3];
						$clabel = 'NULL';
						$cdr3aa = $line[2];
						$cdr3nt = $line[4];
						$unpro = $line[5];
						$ambiguous = $line[6];
						$countdna = $line[7];
						$countpep = $line[8];
						$countdna_after_collapse = $line[9];
						$countpep_after_collapse = $line[10];
						$seq_dna_after_collapse = $line[11];
						$cdr3lenght = sizeof($cdr3nt);
						$count = 1;
						$comment = 'NULL';
												
						if ($countdna_after_collapse == 'NA')
							$countdna_after_collapse = 'NULL';
						if ($countpep_after_collapse ==  'NA')
							$countpep_after_collapse = 'NULL';
						
						echo '<br>PASSAGE :'.$i.'<br>';
						
						$collapsed = 'NULL';
						if ($seq_dna_after_collapse != 'NA') {
							// Get the tcr ID of the sequence collapsed with if it exists
							$query = "SELECT tcr_id from tcr.tcr where tcr_vlabel = '".$vlabel."' and tcr_jlabel = '".$jlabel."'
											   and tcr_cdr3nt ='".$seq_dna_after_collapse."' and tcr_chain = '".$chain."';";	
							echo $query.'<br>';	
							$request = $pdo->prepare($query);
							$request -> execute();
							$aswr = $request->fetch();
							$collapsed = $aswr['tcr_id'];
							echo $collapsed.'<br>';
						}
						
						// If the repertoire is no already present in the database
						// Try to ad the tcr and the corresponding observation in the database
						// On essaie de l'ajouter dans tcr
						try {
							$pdo->beginTransaction();
							$query = "INSERT INTO tcr.tcr VALUES ('".$newpktcr."','".$chain."','".$vlabel."',
										   '".$jlabel."',".$clabel.",'".$cdr3nt."','".$cdr3aa."',".$cdr3lenght.",".$count.",
										   ".$comment.",'".$ambiguous."','".$unpro."')";
							$request = $pdo->prepare($query);
							echo $query.'<br>';
							$request -> execute();
							
							// Si ca marche on l'ajoute aussi dans tcrobs
							// Create an association with the curent repertoire
							$query = "INSERT INTO tcr.tcr_obs VALUES (".$newpktcr.",'".$p."',".$e.",".$o.",
										   '".$s."',".$a.",".$m.",
										   '".$runid."','".$repid."','".$nana."',".$countdna.",".$countpep.",
										   ".$countdna_after_collapse.",".$countpep_after_collapse.",".$collapsed.");";
							$request = $pdo->prepare($query);
							echo $query.'<br>';
							$request -> execute();
							$pdo->commit();
							
							$newpktcr += 1;
						}
						catch (Exception $ex) { 
							$pdo->rollBack();
							echo '<br>=====> ECHEC newpktcr ne change pas :'.$newpktcr.'<br>';
							try {
								$pdo->beginTransaction();
								// Si ca echoue on recherche dans tcr le numero qui correspond
								// on ajoute l'entré dans tcr_obs
								# if the repertory is already present in the database
								# get the PK of the TCR
								$query = "SELECT tcr_id, tcr_count from tcr.tcr where tcr_vlabel = '".$vlabel."' and tcr_jlabel = '".$jlabel."'
											   and tcr_cdr3nt = '".$cdr3nt."' and tcr_chain = '".$chain."'";
								$request = $pdo->prepare($query);
								echo $query.'<br>';
								$request -> execute();
								$aswr = $request->fetch();
								$PK = $aswr['tcr_id'];
								$nb = $aswr['tcr_count'];
								echo $PK.'<br>'.$nb.'<br>';
								
								# Try to add a TCR obs line in the table
								$query = "INSERT INTO tcr.tcr_obs VALUES ('".$PK."','".$p."',
											   ".$e.",".$o.",
											   '".$s."',".$a.",".$m.",'".$runid."','".$repid."',
											   '".$nana."',".$countdna.",".$countpep.",
											   ".$countdna_after_collapse.",".$countpep_after_collapse.",".$collapsed.");";
								$request = $pdo->prepare($query);
								echo $query.'<br>';
								$request -> execute();
								
								$newcount = $nb + 1;
								
								# Modify the count in the tcr
								$query = "UPDATE tcr.tcr SET tcr_count = '".$newcount."' WHERE tcr_id = '".$PK."';";
								$request = $pdo->prepare($query);
								$request -> execute();
								echo $query.'<br>';
								$pdo->commit();
							}
							catch (Exception $ex) {
								$pdo->rollBack();
								echo "zut ...".'<br>';
							}
						}
					}
					$i++;
				}
				if (!feof($file)) {
					echo "<br>error\n<br>";
				}
				fclose($file);
				echo $i;
			}
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
	}
}

// La fonction recoit une ligne
function addstat($div, $b, $date, $i, $keys, $pdo) {
	if ($b) {
		if ($i != 0) {
			//~ echo '<br>'.$div.'<br>';
			$div = explode("\t", $div);
			//~ p($div);
			$label = $div[0];
			$label = explode(' ',$label);
			$label = $label[1];
			$labval1 = 'Sequences('.$label.')';
			$labval2 = 'Seq_after_conta('.$label.')';
			$labval3 = 'Seq_after_CDR3l('.$label.')';
			$labval4 = 'Seq_after_unproductive_or_ambiguous('.$label.')';
			$labval5 = 'Seq_after_single('.$label.')';
			$val1 = $div[1];
			$val2 = $div[2];
			$val3 = $div[3];
			$val4 = $div[4];
			$val5 = $div[5];
			$unit = 'N/A';
			$stcom = $date;
			
			$req = array(
				$labval1 => $val1,
				$labval2 => $val2,
				$labval3 => $val3,
				$labval4 => $val4,
				$labval5 => $val5,
			);

			$query = "INSERT INTO tcr.stat VALUES (:p,:e,:o,:s,:a,:m,:runid,:repid,:nana,:stlabel,:stvalue,:stunit,
			:stcomment)";
			$request = $pdo->prepare($query);
			foreach(array_keys($req) as $v) {
				try {
					$pdo->beginTransaction();
					echo $v;
					echo $req[$v];
					(preg_match("/NA/",$req[$v]) == 1 ? $request -> bindValue(':stvalue',null, PDO::PARAM_STR) : 
								$request -> bindValue(':stvalue',$req[$v], PDO::PARAM_STR));
					($v == '' ? $request -> bindValue(':stlabel',null, PDO::PARAM_STR) : 
								$request -> bindValue(':stlabel',$v, PDO::PARAM_STR));
					
					
					$request -> bindValue(':stunit',$unit, PDO::PARAM_STR);
					$request -> bindValue(':stcomment',$stcom, PDO::PARAM_STR);
					foreach(array_keys($keys) as $v) {
						//~ echo $v;
						//~ echo $keys[$v];
						($keys[$v] == '' ? $request -> bindValue(':'.$v,null, PDO::PARAM_STR) : 
									$request -> bindValue(':'.$v,$keys[$v], PDO::PARAM_STR));
					}
					//~ echo '<br>requete effectué<br>';
					$request -> execute();
					$pdo->commit();
				}
				catch (Exception $ex) { 
					$pdo->rollBack();
				}
			}
		}
	}
}
?>
