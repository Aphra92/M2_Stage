<?php
	session_start();
	//Include
	$title="Modifications";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
// Données recus
//~ p($_GET);
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
													//~ p($val);
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
													and st_label = :st_label
													';
													if ($val[9] == 'J' || $val[9] == 'V' || $val[9] == 'V-J' || $val[9] == 'clonotypes') 
														$temp = '#'.$val[9];
													else 
														$temp = $val[9];
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
													$request -> bindValue(':st_label',$temp);
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
		"ac_id" => "Action",	
		"st_label" => "Stat",	
	);
	$keys = get_liste_key($pdo, 'tcr');
	$schema = 'tcr';
	//~ p($keys);
	$table_rows = array();
	while($row = $request->fetch(PDO::FETCH_ASSOC)) {
		$table_rows[] = $row;
	}
	//p($table_rows);
	$where =  $corres[$id];
	
	// defini le nombre de ligne en fonction du nombre de resultat dans la db
	if (isset($table_rows))
		$countrow = sizeof($table_rows);
	else 
		$countrow = 1;
	
	$fakepost = array (
		"table" => $where,
		"quantity" => "",
		"select" => $countrow,
	);
	//~ p($fakepost);
	
	//keep informations about form
	$save ='';
	foreach (array_keys($fakepost) as $val) {
		$save .= $val.',';
	}
	$save .= $countrow;
	//echo $save;
	
	// Create form	
	// Recup la config du formulaire pour l'ajout de donnée
	$return = config_form($fakepost, $pdo, $schema, TRUE);
	//~ p($return);
	// recupere les infos de chaque colonne
	// [colname] => type,length
	$columns_infos = get_info_columns($pdo, $schema);
	//~ p($columns_infos);
	// Affiche le formulaire sur la page web
	create_form($return["form"], $countrow, $columns_infos, $pdo, $return["foreign"], 
	$return["auto"], $return["opt"], $return["hide"], $return["newkey"], $return["verr"], TRUE, $save);
	
	// rempli le formulaire
	echo "<script>
		var tablerow = ".json_encode($table_rows)."
		$('#corp input[type=text], select').each(
			function(index){  
				var input = $(this);
				str = input.attr('name');
				var strsplit = str.split(\"|\");
				if (strsplit.length == 3) {
					if (input.val() == '') {
						input.val(tablerow[strsplit[1]][strsplit[2]]);
					}
				}
			}
		);
	</script>";
}
?>

<script type="text/javascript">
	// valide la colonne avec un appel ajax et grise // supprime ? les inputs
	function validate(line) {
		var req = {};
		var cur;
		var curdate;
		var curid;
		var i = 0;
		$('#corp input[type=text], select').each(
			function(index){ 
				var input = $(this);
				var name = input.attr('name');
				linename = name.split("|")[1];
				if (linename == line) {
					//~ alert(name);
					var temp = name.split('|');
					if (temp[2].split('_')[1] == 'curation') {
						if (i == 0) {
							cur = name;
							i += 1;
						}
						else {
							if (i == 1) {
								curdate = name;
								i += 1;
							}
							else {
								if (i == 2) {
									curid = name;
								}
							}
						}
					}
					//~ alert(input.val());
					req[""+name] = ""+input.val();
					$('input[name="'+name+'"]').css("background","#50D050");
				}
			}
		);
		var data = JSON.stringify(req);
		//~ alert(data);
		//~ document.write(data);
		$.ajax({
			url: 'includes/curation_update_table.php',
			type: 'POST',
			dataType: 'json', 
			data: 	{
						term: data,
					},  
			success: function(data) {
				for(val in data){
					alert(val);
				}
			},
			error: function() {
				alert('La requete a fonctionné'); 
			}
		}); 
		$('input[name="'+line+'"]').attr('onclick', '');
	}
</script>

<script>
  $( function() {
    $( document ).tooltip();
  } );
</script>

<div id="message"></div>

<script>
/*
  $(function() {
      $(document).ajaxStart(function() {
        $('#message').html('Méthode ajaxStart exécutée<br>');
      });
      $(document).ajaxSend(function(ev, req, options){
        $('#message').append('Méthode ajaxSend exécutée, ');
        $('#message').append('nom du fichier : ' + options.url + '<br>');
      });
      $(document).ajaxStop(function(){
        $('#message').append('Méthode ajaxStop exécutée<br>');
      });
      $(document).ajaxSuccess(function(ev, req, options){
        $('#message').append('Méthode ajaxSuccess exécutée<br>');
        $('#message').append(ev);
      });
      $(document).ajaxComplete(function(ev, req, options){
        $('#message').append('Méthode ajaxComplete exécutée<br>');
      });
      $(document).ajaxError(function(ev, req, options, erreur){
        $('#message').append('Méthode ajaxError exécutée, ');
        $('#message').append('erreur : ' + erreur + '<br>');
      });
      $('#donnees').load('affiche.htm');
    });
*/  
</script>
		
<script type="text/javascript" src="includes/functions.js"></script>
</body>
<?php
	include('./includes/footer.php');
?>
</html>
