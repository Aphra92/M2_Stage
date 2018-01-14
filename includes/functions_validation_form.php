<?php
// Les fonctions de ce fichier servent a la validation du formulaire crée pour new data, curation et modification

// Récupère le nom de la table ou on veut requete en fonction de la clé et de la ou on est
// utilisé pour les requetes d'autocompletion pour savoir si on recherche dans la tale actuelle ou dans une autre
// codé au cas par cas
function getfromtable($current, $f) {
	$fromtable = '';
	if ($current == 'manip') {
		if ($f == 'm_id' || $f == 'm_input') {
			$fromtable = 'manip';
		}
		else {
			if ($f == 'p_id' || $f == 'e_id' || $f == 'o_id' || $f == 's_id' || $f == 'a_id') {
				// On requete dans aliquot pour les p / f (droit)
				$fromtable = 'aliquot';
			}
			else {
				if ($f == 'pr_name') {
					// On requete dans aliquot pour les p / f (droit)
					$fromtable = 'protocole';
				}
			}
		}
	}
	else {
		if ($current == 'action') {
			if ($f == 'p_id1' || $f == 'e_id1' || $f == 'o_id1' ){
				$fromtable = 'organism';
			}
			else {
				if ($f == 'p_id2' || $f == 'e_id2' || $f == 'o_id2' || $f == 's_id2' ) {
					$fromtable = 'sample';
				}
				else {
					if ($f == 'ac_id')
						$fromtable = 'action';
				}
			}
		}
		else {
			if ($current == 'protocole'){
				$fromtable = 'protocole';
			}
			else {
				if ($current == 'run'){
					$fromtable = 'run';
				}
				else {
					if ($current == 'project'){
						$fromtable = 'project';
					}
					else {
						if ($current == 'experiment'){
							if ($f == 'p_id')
								$fromtable = 'project';
							else {
								if ($f == 'e_id')
									$fromtable = 'experiment';
							}
						}
						else {
							if ($current == 'organism'){
								if ($f == 'p_id' || $f == 'e_id') {
									$fromtable = 'experiment';
								}
								else {
									if ($f == 'o_id')
										$fromtable = 'organism';
								}
							}
							else {
								if ($current == 'sample'){
									if ($f == 'p_id' || $f == 'e_id' || $f == 'o_id'|| $f == 's_exp_group1')
										$fromtable = 'organism';
									if ($f == 's_id')
										$fromtable = 'sample';
								}
								else {
									if ($current == 'aliquot'){
										if ($f == 'p_id' || $f == 'e_id' || $f == 'o_id'|| $f == 's_id')
											$fromtable = 'sample';
										if ($f == 'a_id')
											$fromtable = 'aliquot';
									}
									else {
										if ($current == 'repertoire') {
											if ($f == 'p_id' || $f == 'e_id' || $f == 'o_id'|| $f == 's_id' || $f == 'a_id' || $f == 'm_id')
												$fromtable = 'manip';
											if ($f == 'run_id')
												$fromtable = 'run';
											if ($f == 'n_ana' || $f == 'rep_id')
												$fromtable = 'repertoire';
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
	return $fromtable;
}

// Création des scripts pour vérifier ce qui est rentré dans les formulaires
// Le traitement est diffrent en fonction de l'array
function check_attribution_colonnes($pdo, $form, $countrow, $foreign, $auto, $newkey, $choice) {
	//~ p($foreign);
	//~ p($newkey);
	//~ p($auto);
	//~ p($form);
	$newtmp = array();
	// Pour toutes les nouvelles clés
	foreach ($newkey as $f) {
		$newtmp[] = $f;
		// recup la table actuelle
		$t = explode(',', $form[$f]);
		$current = array_shift($t);
		//~ echo '<br> NEWKEY ::: '.$f.' '.$form[$f].'     current : '.$current.'<br>';
		//~ echo "input[name=\"".$form[$f]."|i|".$f."\"]";
		// Crée l'autocomplétion
		$fromtable = getfromtable($current, $f);
		autocomp_dynamique_forbid_patt($f, $fromtable, $pdo, $countrow, $form, $current);
		complementaryscript($f, $countrow, $form, $current, $choice);
	}
	// Pour toutes les clés étrangères
	foreach ($foreign as $f) {
		if (!in_array($f, $newtmp)) {
		// recup la table actuelle
			$t = explode(',', $form[$f]);
			$current = array_shift($t);
			//~ echo '<br> FOREIGN ::: '.$f.' '.$form[$f].' '.$current.'<br>';
			//~ echo "input[name=\"".$form[$f]."|i|".$f."\"]";
			// recup la table ou on veut requete
			$fromtable = getfromtable($current, $f);
			// Autocompletion
			autocomp_dynamique_foreign_key($f, $fromtable, $pdo, $countrow, $form, $current);
			complementaryscript($f, $countrow, $form, $current, $choice);
		}
	}
	// Pour toutes les clés ou on veut de l'autocompletion
	foreach ($auto as $f) {
		// recup la table actuelle
		$t = explode(',', $form[$f]);
		$current = array_shift($t);
		//~ echo '<br> AUTO ::: '.$f.' '.$form[$f].' '.$current.'<br>';
		//~ echo "input[name=\"".$form[$f]."|i|".$f."\"]";
		// Autocomplétion
		autocomp_all($f, $countrow, $form);
		complementaryscript($f, $countrow, $form, $current, $choice);
	}
}

// Function pour requete sur l'ensemble de la colonne d'une table
function checkrequest($f, $fromtable, $pdo) {
	$req = $f;
	if ($f == 's_exp_group1')
		$req = 'o_exp_group1';
	$requete = "SELECT distinct(".$req.") FROM tcr.".$fromtable;
	//~ echo "<br>".$requete;
	$result = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
	$suggestions = array();
	while($donnees = $result->fetch(PDO::FETCH_ASSOC)) {
	// On ajoute les données dans un tableau
	$suggestions['suggestions'][] = strval($donnees[$req]);
	}
	return $suggestions;
}

/* Fonction pour générer de l'autocompletion dynamique ainsi qu'un pattern dynamique qui autorise a ecrire seulement
 ce qui n'existe pas deja dans la base
 la fonction fait un appel Ajax vers le fichier autocomp_dynamique_forbid_patt.php et envoi de facon systematique la valeur des clés possibles
 qui contient les requetes php */
function autocomp_dynamique_forbid_patt($f, $fromtable, $pdo, $countrow, $form, $current) {
	for ($i = 0; $i < $countrow; $i++) {
		echo "<script>
			$(function() {
				var sel = '$f';
				var fr = '$fromtable';
				var curr = '$current';
				$('input[name=\"".$form[$f]."|".$i."|".$f."\"]').autocomplete({
					source : function(requete, reponse){
						$.ajax({
							url: 'includes/autocomp_dynamique.php',
							type: 'POST',
							dataType: 'json', 
							data: 	{
										term: requete.term,
										select : sel,
										from : fr,
										cur : curr,
										p : 	$('input[name=\"".$form[$f]."|".$i."|p_id\"]').val(), 
										e : 	$('input[name=\"".$form[$f]."|".$i."|e_id\"]').val(),
										o : 	$('input[name=\"".$form[$f]."|".$i."|o_id\"]').val(),
										s : 	$('input[name=\"".$form[$f]."|".$i."|s_id\"]').val(),
										a : 	$('input[name=\"".$form[$f]."|".$i."|a_id\"]').val(),
										m : 	$('input[name=\"".$form[$f]."|".$i."|m_id\"]').val(),
										run : 	$('input[name=\"".$form[$f]."|".$i."|run_id\"]').val(),
										rep : 	$('input[name=\"".$form[$f]."|".$i."|rep_id\"]').val()
									},  
							success: function(data) {
								reponse($.map(data, function(objet){
									//alert(objet);
									var obj = $('input[name=\"".$form[$f]."|".$i."|".$f."\"]');
									var patt = '';
									for (var j = 0; j < objet.length; j++) {
										patt += patt + objet[j] + '|';
									}
									//alert(patt);
									patt = patt.substring(0, patt.length - 1);
									//patt = '^((?!(?:'+patt+')$)[0-9]*)$';
									patt = '^((?!(?:'+patt+')$)[a-zA-Z0-9_]*)$';
									$(obj).attr('pattern', patt);
									return objet;
								}));
							},
							error: function() {
								//alert('La requête n\'a pas abouti'); 
							}
						}); 
					},
					minLength: 0,
				})
				.focus(function() {
					$(this).autocomplete('search', $(this).val());
				});
			});
		</script>";
	}
}

/* Fonction qui génére de l'autocompletion sur la colonne actuelle de la table actuelle 
 * La fonction retourne tout ce qui est présent dans la colonne 
 * Si le nom de la colonne commence par resp ou part, la colonne de reference devient identifier
 * et la table de reference devient entity */
function autocomp_all($f, $countrow, $form) {
	$start = explode('_', $f);
	if ($start[0] == 'resp' || $start[0] == 'part') {
		$se = 'identifier';
		$from = 'entity';
		$rep = ",";
		if ($start == 'part') {
			// Ajout un return false pour empecher de pouvoir cliquer dessus
			$rep = ",
					select: function (event, ui) {
						return false;
					},";
		}
	}
	else {
		if ($start[1] == 'curation') {
			if (isset($start[2])){
				$se = 'identifier';
				$from = 'entity';
				$rep = ",";
			} 
			else {
				$se = $f;
				$from = $form[$f];
				$rep = ",";
			}
		}
		else {
			$se = $f;
			$from = $form[$f];
			$rep = ",";
		}
	}
	// Creation du script pour autocompletion
	for ($i = 0; $i < $countrow; $i++) {
		echo "<script>
			$(function() {
				var sel = '$se';
				var fr = '$from';
				$('input[name=\"".$form[$f]."|".$i."|".$f."\"]').autocomplete({
					source : function(requete, reponse){
						$.ajax({
							url: 'includes/autocomp_all.php',
							type: 'POST',
							dataType: 'json', 
							data: 	{
										term: requete.term,
										select : sel,
										from : fr
									},  
							success: function(data) {
								reponse($.map(data, function(objet){
									//alert(objet);
									return objet;
								}));
							},
							error: function() {
								//alert('La requête n\'a pas abouti'); 
							}
						}); 
					},
					minLength: 0";
					echo $rep;
		echo	"})
				.focus(function() {
					$(this).autocomplete('search', $(this).val());
				});  
			});
		</script>";
	}
}

/* Fonction pour générer de l'autocompletion pour les clés étrangères (dynamique)
 * Si la clé est différentes que le nom d'origine elle est changé au debut de la fonction (cas pour action)
 * Crée un pattern pour empecherla validation de quelqu chose qui n'est pas dans la base
 * La fonction empeche d'écrire quelque chose qui n'est pas présent dans la bdd
 */
function autocomp_dynamique_foreign_key($f, $fromtable, $pdo, $countrow, $form, $current) {
	// Change le nom de la clé
	if ($current == 'action') {
		if ($f == 'p_id1') {
			$ft = 'p_id';
			$m = 1;
		}
		if ($f == 'e_id1') {
			$ft = 'e_id';
			$m = 1;
		}
		if ($f == 'o_id1') {
			$ft = 'o_id';
			$m = 1;
		}
		if ($f == 'p_id2') {
			$ft = 'p_id';
			$m = 2;
		}
		if ($f == 'e_id2') {
			$ft = 'e_id';
			$m = 2;
		}
		if ($f == 'o_id2') {
			$ft = 'o_id';
			$m = 2;
		}
		if ($f == 's_id2') {
			$ft = 's_id';
			$m = 2;
		}
		$result = checkrequest($ft, $fromtable, $pdo);	
	}
	else {
		$result = checkrequest($f, $fromtable, $pdo);
		$m = "";
	}
	// Création du pattern a partir du resulat de la requete sur l'ensemble de la colonne
	$str = '"';
	$patt = '';
	foreach ($result as $val){
		foreach ($val as $v){
			$str .= $v.',';
			$patt .= ''.$v.'|';
		}
	}
	$str = substr($str, 0, -1);
	$patt = substr($patt, 0, -1);
	$str .= '"';
	$patt .= '';
	// Creation du script pour surveiler et empecher de valider le formulaire si non rempli
	for ($i = 0; $i < $countrow; $i++) {
		echo "<script>
			var previousValue = '';
			var obj = $('input[name=\"".$form[$f]."|".$i."|".$f."\"]');
			var re = /[^ \w]/;
			$(obj).attr('pattern', '".$patt."');
			var name$f = '".$f."';
			var stringToSplit_$f = ".$str.";

			$(function() {
				var sel = '$f';
				var fr = '$fromtable';
				var curr = '$current';
				$('input[name=\"".$form[$f]."|".$i."|".$f."\"]').autocomplete({
					source : function(requete, reponse){
						$.ajax({
							url: 'includes/autocomp_dynamique.php',
							type: 'POST',
							dataType: 'json', 
							data: 	{
										term: requete.term,
										select : sel,
										from : fr,
										cur : curr,
										p : 	$('input[name=\"".$form[$f]."|".$i."|p_id".$m."\"]').val(), 
										e : 	$('input[name=\"".$form[$f]."|".$i."|e_id".$m."\"]').val(),
										o : 	$('input[name=\"".$form[$f]."|".$i."|o_id".$m."\"]').val(),
										s : 	$('input[name=\"".$form[$f]."|".$i."|s_id".$m."\"]').val(),
										a : 	$('input[name=\"".$form[$f]."|".$i."|a_id".$m."\"]').val(),
										m : 	$('input[name=\"".$form[$f]."|".$i."|m_id\"]').val()
									},  
							success: function(data) {
								//alert(data);
								reponse($.map(data, function(objet){
									//alert(objet);
									return objet;
								}));
							},
							error: function() {
								//alert('La requête n\'a pas abouti'); 
							}
						}); 
					},
					minLength: 0,
				})
				.focus(function() {
					$(this).autocomplete('search', $(this).val());
				})
				.keyup(function() {
					//alert(name$f);
					//alert(stringToSplit_$f);
					var isValid = false;
					var str = this.value
					var tab = stringToSplit_$f.split(',')
					for (i in tab) {
						if (!str.match(re)) {
							if (tab[i].toLowerCase().match(str.toLowerCase())) {
								//alert(tab[i]);
								isValid = true;
								continue;
							}
						}
					}
					if (!isValid) {
						this.value = previousValue;
					} else {
						previousValue = this.value;
					}
				});
			});
		</script>";
	}
}

// Script a ajouter au cas par cas
// Ajoute des scripts spécifiques pour chacun des form générés	
// Les script javascript ajouté permette l'autocomplétion de certain champs du formulaire,
// l'ajout d'information dans le tooltip, l'autosuppresion de certain champ si d'autre sont rempli etc ...
function complementaryscript($f, $countrow, $form, $current, $choice) {
	// $choice = 0 quand newdata
	// $choice = 1 quand curation
	// Si on entre une manip
	if ($current == 'manip') {
		// Dans manip, verifie la valeur de m_id quand on focusout
		// Si m_id = 1, on met aliquot dans input et on rempli les références avec null pour la manip precedente
		// Sinon, on recopie les valeurs p, e, o, s, a pour cibler l'aliquot défini
		// On met aussi la valeur excluded a false par defaut
		if ($f == 'pr_name') {
			for ($i = 0; $i < $countrow; $i++) {
				echo '<script>
					$(\'input[name="manip,participe_m|'.$i.'|m_id"]\').focusout(function() {
						if ($(\'input[name="manip,participe_m|'.$i.'|m_id"]\').val() == 1 ) {
							$(\'input[name="manip|'.$i.'|m_input"]\').val("aliquot");
							$(\'input[name="manip|'.$i.'|p_idprev"]\').attr("required", false);
							$(\'input[name="manip|'.$i.'|e_idprev"]\').attr("required", false);
							$(\'input[name="manip|'.$i.'|o_idprev"]\').attr("required", false);
							$(\'input[name="manip|'.$i.'|s_idprev"]\').attr("required", false);
							$(\'input[name="manip|'.$i.'|a_idprev"]\').attr("required", false);
							$(\'input[name="manip|'.$i.'|m_idprev"]\').attr("required", false);
							$(\'input[name="manip|'.$i.'|p_idprev"]\').val("");
							$(\'input[name="manip|'.$i.'|e_idprev"]\').val("");
							$(\'input[name="manip|'.$i.'|o_idprev"]\').val("");
							$(\'input[name="manip|'.$i.'|s_idprev"]\').val("");
							$(\'input[name="manip|'.$i.'|a_idprev"]\').val("");
							$(\'input[name="manip|'.$i.'|m_idprev"]\').val("");
						}
						else {
							$(\'input[name="manip|'.$i.'|p_idprev"]\').attr("required", true);
							$(\'input[name="manip|'.$i.'|e_idprev"]\').attr("required", true);
							$(\'input[name="manip|'.$i.'|o_idprev"]\').attr("required", true);
							$(\'input[name="manip|'.$i.'|s_idprev"]\').attr("required", true);
							$(\'input[name="manip|'.$i.'|a_idprev"]\').attr("required", true);
							$(\'input[name="manip|'.$i.'|m_idprev"]\').attr("required", true);
							$(\'input[name="manip|'.$i.'|p_idprev"]\').val($(\'input[name="manip,participe_m|'.$i.'|p_id"]\').val());
							$(\'input[name="manip|'.$i.'|e_idprev"]\').val($(\'input[name="manip,participe_m|'.$i.'|e_id"]\').val());
							$(\'input[name="manip|'.$i.'|o_idprev"]\').val($(\'input[name="manip,participe_m|'.$i.'|o_id"]\').val());
							$(\'input[name="manip|'.$i.'|s_idprev"]\').val($(\'input[name="manip,participe_m|'.$i.'|s_id"]\').val());
							$(\'input[name="manip|'.$i.'|a_idprev"]\').val($(\'input[name="manip,participe_m|'.$i.'|a_id"]\').val());
							$(\'input[name="manip|'.$i.'|m_idprev"]\').val($(\'input[name="manip,participe_m|'.$i.'|m_id"]\').val()-1);
						}
					});	
				</script>';
				// Quand on choisi un protocole,
				// on recupere les informations sur le protocole (requete (get protocole)) et on ajoute en titre de l'input qui correspond 
				// le nom de la valeur a entré, son unité et on passe l'input en required si la valeur est obligatoire
				echo '<script>
					$(\'input[name="manip|'.$i.'|pr_name"]\').focusout(function() {
						 $.ajax({
							url: "includes/getprotocole.php",
							type: "POST",
							dataType: "json",
							async: !1, 
							data: 	{
										term: $(\'input[name="manip|'.$i.'|pr_name"]\').val()
									},  
							success: function(data) {
								$.map(data, function(objet){
									var prot$f = eval(objet);
									var i = 1;
									for (var j = 0; j < prot$f.length; j += 3) {
										if (prot$f[j+1] == 1) {
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').prop(\'disabled\', false);
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').attr("required", true);
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').prop("title", \'\'+ prot$f[j] +\' unit : \'+ prot$f[j+2]);
										}
										else {
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').attr("required", false);
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').prop("title", \'\');
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').prop(\'disabled\', true);
											$(\'input[name="manip|'.$i.'|m_val\'+i+\'"]\').val("");
										}
										i++;
									}
									return objet;
								});
							},
							error: function() {
								alert("La requête n\'a pas abouti"); 
							}
						});
					});
				</script>';
			}
			if ($choice == 0) {
				for ($i = 0; $i < $countrow; $i++) {
					// Autoremplissage du champ exclude sur false
					echo '<script>
						$(\'input[name="manip|'.$i.'|m_excluded"]\').val(false);
					</script>';
				}
			}
			if ($choice == 1) {
				for ($i = 0; $i < $countrow; $i++) {
					// récuperation de la date du jour et autoremplissage
					echo '<script>
						var today = new Date();
						var d = today.getDate();
						var m = today.getMonth()+1; //January is 0!
						var y = today.getFullYear();

						if(d<10) {
							d="0"+d
						} 

						if(m<10) {
							m="0"+m
						} 

						today = y+"/"+m+"/"+d;
						
						$(\'input[name="manip|'.$i.'|m_curation_date"]\').val(today);
					</script>';
				}
			}
		}
		else {
			// A chaque fois qu'on écrit dans une clé (p/e/o/s/a), on recopie la valeur dans idprev qui correspond
			// si m_id est != 1
			if ($f == 'p_id' || $f == 'e_id' || $f == 'o_id' || $f == 's_id' || $f == 'a_id') {
				for ($i = 0; $i < $countrow; $i++) {
					echo '<script>
						$(\'input[name="manip,participe_m|'.$i.'|'.$f.'"]\').focusout(function() {
							if ($(\'input[name="manip,participe_m|'.$i.'|m_id"]\').val() != 1) {
								$(\'input[name="manip|'.$i.'|'.$f.'prev"]\').val($(\'input[name="manip,participe_m|'.$i.'|'.$f.'"]\').val());
							}
						});
						</script>';
				}
			}
		}
	}
	else {
		// Si on entre une action
		if ($current == 'action') {
			// On change les $f pour pouvoir faire les requetes
			if ($f == 'p_id1' || $f == 'e_id1' || $f == 'o_id1' || $f == 'p_id2' || $f == 'e_id2' || $f == 'o_id2') {
				if ($f == 'p_id1') {
					$ft = 'p_id';
					$m = 2;
				}
				if ($f == 'e_id1') {
					$ft = 'e_id';
					$m = 2;
				}
				if ($f == 'o_id1') {
					$ft = 'o_id';
					$m = 2;
				}
				if ($f == 'p_id2') {
					$ft = 'p_id';
					$m = 1;
				}
				if ($f == 'e_id2') {
					$ft = 'e_id';
					$m = 1;
				}
				if ($f == 'o_id2') {
					$ft = 'o_id';
					$m = 1;
				}
				// Si on rempli les clé de organisme, on passe les clé de sample en not required et inversement
				// On passe en required le reste des input qui correspondent a la ou on a ecrit
				// Si on écrit d'un coté on supprime les valeurs qui ont été rentré de l'autre coté
				for ($i = 0; $i < $countrow; $i++) {
					echo '<script>
						$(\'input[name="action|'.$i.'|'.$f.'"]\').focusout(function() {
							if ($.trim($(\'input[name="action|'.$i.'|'.$f.'"]\').val()) != "" ) {
								if ('.$m.' == 2) { 
									$(\'input[name="action|'.$i.'|p_id2"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|e_id2"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|o_id2"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|s_id2"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|p_id2"]\').val("");
									$(\'input[name="action|'.$i.'|e_id2"]\').val("");
									$(\'input[name="action|'.$i.'|o_id2"]\').val("");
									$(\'input[name="action|'.$i.'|s_id2"]\').val("");
									$(\'input[name="action|'.$i.'|p_id1"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|e_id1"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|o_id1"]\').attr("required", true);
								}
								if ('.$m.' == 1) { 
									$(\'input[name="action|'.$i.'|p_id1"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|e_id1"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|o_id1"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|p_id1"]\').val("");
									$(\'input[name="action|'.$i.'|e_id1"]\').val("");
									$(\'input[name="action|'.$i.'|o_id1"]\').val("");
									$(\'input[name="action|'.$i.'|p_id2"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|e_id2"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|o_id2"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|s_id2"]\').attr("required", true);
								}
							}						
						});
						</script>';
				}
			}
			else {
				// Si on ecrit dans sid2, on efface ce qui est ecrit dans les clé 1 et on passe required sur false
				// Les clés 2 passe en requiered			
				if ($f == 'ac_type') {
					for ($i = 0; $i < $countrow; $i++) {
						echo '<script>
							$(\'input[name="action|'.$i.'|s_id2"]\').focusout(function() {
								if ($.trim($(\'input[name="action|'.$i.'|'.$f.'"]\').val()) != "" ) {
									$(\'input[name="action|'.$i.'|p_id1"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|e_id1"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|o_id1"]\').attr("required", false);
									$(\'input[name="action|'.$i.'|p_id1"]\').val("");
									$(\'input[name="action|'.$i.'|e_id1"]\').val("");
									$(\'input[name="action|'.$i.'|o_id1"]\').val("");
									$(\'input[name="action|'.$i.'|p_id2"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|e_id2"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|o_id2"]\').attr("required", true);
									$(\'input[name="action|'.$i.'|s_id2"]\').attr("required", true);
								}
							});
							</script>';
					}
					if ($choice == 1) {
						// récuperation de la date du jour et autoremplissage
						for ($i = 0; $i < $countrow; $i++) {
							echo '<script>
								var today = new Date();
								var d = today.getDate();
								var m = today.getMonth()+1; //January is 0!
								var y = today.getFullYear();
								if(d<10) {
									d="0"+d
								} 
								if(m<10) {
									m="0"+m
								} 
								today = y+"/"+m+"/"+d;
								$(\'input[name="action|'.$i.'|ac_curation_date"]\').val(today);
							</script>';
						}
					}
				}
			}
		}
		else {
			if ($current == 'organism') {
				// Si on est dans organism, on rempli le champ excluded sur false par defaut
				// on met des titre sur les ages pour demander de le mettre en wks
				// On copie par defaut l'age 1 dans l'age 2
				if ($f == 'o_age_1') {
					$fbis = 'o_age_2';
					for ($i = 0; $i < $countrow; $i++) {
						echo '<script>
							$(\'input[name="organism|'.$i.'|'.$f.'"]\').prop("title", "in days");
							$(\'input[name="organism|'.$i.'|'.$fbis.'"]\').prop("title", "in wks for mouse \nin year for human");
							$(\'input[name="organism|'.$i.'|'.$f.'"]\').focusout(function() {
								$(\'input[name="organism|'.$i.'|'.$fbis.'"]\').val($(\'input[name="organism|'.$i.'|'.$f.'"]\').val());
							});
						</script>';
					}
				}
				else {
					// On recopie automatiquement la valeur de l'experimental group1 dans 2
					if ($f == 'o_exp_group1') {
						$fbis = 'o_exp_group2';
						for ($i = 0; $i < $countrow; $i++) {
							echo '<script>
								$(\'input[name="organism|'.$i.'|'.$f.'"]\').focusout(function() {
									$(\'input[name="organism|'.$i.'|'.$fbis.'"]\').val($(\'input[name="organism|'.$i.'|'.$f.'"]\').val());
								});
							</script>';
						}
						if ($choice == 0) {
							for ($i = 0; $i < $countrow; $i++) {
								echo '<script>
									$(\'input[name="organism|'.$i.'|o_excluded"]\').val(false);
								</script>';
							}
						}
						if ($choice == 1) {
							// récuperation de la date du jour et autoremplissage
							for ($i = 0; $i < $countrow; $i++) {
								echo '<script>
									var today = new Date();
									var d = today.getDate();
									var m = today.getMonth()+1; //January is 0!
									var y = today.getFullYear();
									if(d<10) {
										d="0"+d
									} 
									if(m<10) {
										m="0"+m
									} 
									today = y+"/"+m+"/"+d;
									$(\'input[name="organism|'.$i.'|o_curation_date"]\').val(today);
								</script>';
							}
						}
					}
				}
			}
			else {
				if ($current == 'sample') {
					// Si on est dans p_id, on place les script pour verifier organ, cell, sort
					// Les script verifie que les 3 cases sont remplis et remplise la 4ème de facon automatique en checkant dans les
					// tableau la valeur qui correspond
					if ($f == 's_organ') {
						for ($i = 0; $i < $countrow; $i++) {
							echo '<script>
								var organtab = {
									Spleen:"SPL", 
									Blood:"BL", 
									Brain:"BR", 
									Inguinal_LN:"ILN", 
									Pancreatic_LN:"PLN", 
									Renal_LN:"RLN", 
									Brachial_LN:"BLN", 
									Lymph_node:"LN", 
									Thymus:"THY",
									Liver:"LIV",
									Mesenteric_LN:"MLN",
									Para_aortic_LN:"PALN",
									Pancreatic_Infiltrat_Lymphocytes:"PIL"
								};
								var celltab = {
									Lymphocyte:"LY", 
									Leukocyte:"LK", 
									Splenocyte:"SP", 
									Total:"TO", 
									Thymocyte:"TH", 
									Hepatocyte:"HE", 
								};
								var sorttab = {
									CD4plus:"CD4", 
									CD8plus:"CD8", 
									Total:"TOT", 
									CD4moins:"CD4-", 
									CD8moins:"CD8-", 
									Pool:"Pool", 
									CD4plusCD25hiCD44hiCD62Llo:"amTregs", 
									CD4plusCD25hiCD44loCD62Lhi:"nTregs", 
									CD4plusCD25moins:"Teff", 
									CD3plus:"CD3+", 
									CD4plusGFPmoins:"Teff", 
									CD4plusCD8plus:"DP", 
									CD4plusGFPplus:"Tregs", 
									CD8plusGFPplus:"CD8regs", 
									CD8plusGFPmoins:"CD8", 
									CD3moinsCD4plusCD8plus:"CD3-DP", 
									CD3KO:"CD3KO", 
								};
								$(\'input[name="sample|'.$i.'|s_sort"]\').focusout(function() {
									var organ = $(\'input[name="sample|'.$i.'|s_organ"]\').val();
									var cell = $(\'input[name="sample|'.$i.'|s_cell"]\').val();
									var sort = $(\'input[name="sample|'.$i.'|s_sort"]\').val();
									organ = organ.replace(/ /g, "_");
									organ = organ.replace(/-/g, "_");
									sort = sort.replace(/-/g, "moins");
									sort = sort.replace(/\+/g, "plus");
									if (organ != "" && cell != "" && sort != "")
										$(\'input[name="sample|'.$i.'|s_cell_sample"]\').val(\'\' + 
										organtab[organ] +"-"+ celltab[cell] +"-"+ sorttab[sort]);
								});
								$(\'input[name="sample|'.$i.'|s_cell"]\').focusout(function() {
									var organ = $(\'input[name="sample|'.$i.'|s_organ"]\').val();
									var cell = $(\'input[name="sample|'.$i.'|s_cell"]\').val();
									var sort = $(\'input[name="sample|'.$i.'|s_sort"]\').val();
									organ = organ.replace(/ /g, "_");
									organ = organ.replace(/-/g, "_");
									sort = sort.replace(/-/g, "moins");
									sort = sort.replace(/\+/g, "plus");
									if (organ != "" && cell != "" && sort != "")
										$(\'input[name="sample|'.$i.'|s_cell_sample"]\').val(\'\' + 
										organtab[organ] +"-"+ celltab[cell] +"-"+ sorttab[sort]);
								});
								$(\'input[name="sample|'.$i.'|s_organ"]\').focusout(function() {
									var organ = $(\'input[name="sample|'.$i.'|s_organ"]\').val();
									var cell = $(\'input[name="sample|'.$i.'|s_cell"]\').val();
									var sort = $(\'input[name="sample|'.$i.'|s_sort"]\').val();
									organ = organ.replace(/ /g, "_");
									organ = organ.replace(/-/g, "_");
									sort = sort.replace(/-/g, "moins");
									sort = sort.replace(/\+/g, "plus");
									if (organ != "" && cell != "" && sort != "")
										$(\'input[name="sample|'.$i.'|s_cell_sample"]\').val(\'\' + 
										organtab[organ] +"-"+ celltab[cell] +"-"+ sorttab[sort]);
								});
							</script>';
						}
					}
					else {
						// On recopie la valeur de experimental group 1 dans 2 par defaut
						if ($f == 's_exp_group1') {
							$fbis = 's_exp_group2';
							for ($i = 0; $i < $countrow; $i++) {
								echo '<script>
									$(\'input[name="sample|'.$i.'|'.$f.'"]\').focusout(function() {
										$(\'input[name="sample|'.$i.'|'.$fbis.'"]\').val($(\'input[name="sample|'.$i.'|'.$f.'"]\').val());
									});
								</script>';
								
								// script pour desactiver le formulaire si l'action n'a pas été créé
								// recuperation de p / e / o on focusout de p / e / o 
								// et affichage d'une fenetre d'alerte si l'action n'existe pas
								// + desactivation de la ligne
								echo '<script>
									$(\'input[name="sample,participe_s|'.$i.'|o_id"]\').focusout(function() {
										$.ajax({
											url: "includes/getaction.php",
											type: "POST",
											async: !1, 
											dataType:"json",
											data: 	{
														p : $(\'input[name="sample,participe_s|'.$i.'|p_id"]\').val(),
														e : $(\'input[name="sample,participe_s|'.$i.'|e_id"]\').val(),
														o : $(\'input[name="sample,participe_s|'.$i.'|o_id"]\').val()
													},  
											success: function(data) {
											},
											error: function() {
												alert("Something went wrong, no sampling action founded, please create it as soon as possible"); 
											}
										});
									});
								</script>';
								// script pour recopier p/e/o dans p/e/o de action
								// rend inactif p/e/o/s
								/*echo '<script>
									$(\'input[name="action|'.$i.'|p_id2"]\').prop(\'disabled\', true);
									$(\'input[name="action|'.$i.'|e_id2"]\').prop(\'disabled\', true);
									$(\'input[name="action|'.$i.'|o_id2"]\').prop(\'disabled\', true);
									$(\'input[name="action|'.$i.'|s_id2"]\').prop(\'disabled\', true);
									$(\'input[name="action|'.$i.'|ac_type"]\').prop(\'disabled\', true);
									$(\'input[name="action|'.$i.'|ac_type"]\').val(\'sampling\');
									
									$(\'input[name="sample,participe_s|'.$i.'|p_id"]\').focusout(function() {
										$(\'input[name="action|0|p_id1"]\').val($(\'input[name="sample,participe_s|'.$i.'|p_id"]\').val());
										$(\'input[name="action|0|p_id1"]\').prop(\'disabled\', true);
									});
									$(\'input[name="sample,participe_s|'.$i.'|e_id"]\').focusout(function() {
										$(\'input[name="action|0|e_id1"]\').val($(\'input[name="sample,participe_s|'.$i.'|e_id"]\').val());
										$(\'input[name="action|0|e_id1"]\').prop(\'disabled\', true);
									});
									$(\'input[name="sample,participe_s|'.$i.'|o_id"]\').focusout(function() {
										$(\'input[name="action|0|o_id1"]\').val($(\'input[name="sample,participe_s|'.$i.'|o_id"]\').val());
										$(\'input[name="action|0|o_id1"]\').prop(\'disabled\', true);
									});
								</script>';*/
							}
							if ($choice == 0) {
								for ($i = 0; $i < $countrow; $i++) {
									echo '<script>
										$(\'input[name="sample|'.$i.'|s_excluded"]\').val(false);
									</script>';
								}
							}
							if ($choice == 1) {
								for ($i = 0; $i < $countrow; $i++) {
									echo '<script>
										var today = new Date();
										var d = today.getDate();
										var m = today.getMonth()+1; //January is 0!
										var y = today.getFullYear();
										if(d<10) {
											d="0"+d
										} 
										if(m<10) {
											m="0"+m
										} 
										today = y+"/"+m+"/"+d;
										$(\'input[name="sample|'.$i.'|s_curation_date"]\').val(today);
									</script>';
								}
							}
						}
					}
				}
				else {
					// Dans aliquot, on met excluded sur false par default
					if ($current == 'aliquot'){
						if ($f == 'a_type') {
							if ($choice == 0) {
								for ($i = 0; $i < $countrow; $i++) {
									echo '<script>
										$(\'input[name="aliquot|'.$i.'|a_excluded"]\').val(false);
									</script>';
								}
							}
							if ($choice == 1) {
								for ($i = 0; $i < $countrow; $i++) {
									echo '<script>
										var today = new Date();
										var d = today.getDate();
										var m = today.getMonth()+1; //January is 0!
										var y = today.getFullYear();
										if(d<10) {
											d="0"+d
										} 
										if(m<10) {
											m="0"+m
										} 
										today = y+"/"+m+"/"+d;
										$(\'input[name="aliquot|'.$i.'|a_curation_date"]\').val(today);
									</script>';
								}
							}
						}
					}
					else {
						// Dans repertoire
						if ($current == 'repertoire') {
							// On met excluded sur false par default
							// Dans p_id, on regarde les valeur de clé et on crée automatiquement le rep_id
							// On crée aussi automatiquement l'emplacement par default en sortant de run_id
							if ($f == 'rep_meth_id_tcr') {
								for ($i = 0; $i < $countrow; $i++) {
										echo '<script>
											$(\'input[name="repertoire|'.$i.'|p_id"]\').focusout(function() {
												if ($(\'input[name="repertoire|'.$i.'|p_id"]\').val() != "" && 
													$(\'input[name="repertoire|'.$i.'|o_id"]\').val() != "" && 
													$(\'input[name="repertoire|'.$i.'|s_id"]\').val() != "" &&
													$(\'input[name="repertoire|'.$i.'|a_id"]\').val() != "" &&
													$(\'input[name="repertoire|'.$i.'|m_id"]\').val() != "" ) {
													
														var valo = $(\'input[name="repertoire|'.$i.'|o_id"]\').val();
														if (valo.length == 1) {
															var valo = 0 + valo;
														}
														$(\'input[name="repertoire|'.$i.'|rep_id"]\').val(\'\' + 
														$(\'input[name="repertoire|'.$i.'|p_id"]\').val() +"_"+ 
														valo +"_"+ 
														$(\'input[name="repertoire|'.$i.'|s_id"]\').val() +"_"+ 
														$(\'input[name="repertoire|'.$i.'|a_id"]\').val() +"_"+ 
														$(\'input[name="repertoire|'.$i.'|m_id"]\').val());
												}
											});
											$(\'input[name="repertoire|'.$i.'|run_id"]\').focusout(function() {
												if ($(\'input[name="repertoire|'.$i.'|p_id"]\').val() != "" && 
													$(\'input[name="repertoire|'.$i.'|o_id"]\').val() != "" && 
													$(\'input[name="repertoire|'.$i.'|s_id"]\').val() != "" &&
													$(\'input[name="repertoire|'.$i.'|a_id"]\').val() != "" &&
													$(\'input[name="repertoire|'.$i.'|m_id"]\').val() != "" ) {
													
														var dossier = $(\'input[name="repertoire|'.$i.'|run_id"]\').val();
														dossier = dossier.replace(/\d+/g, \'\');
														
														var valo = $(\'input[name="repertoire|'.$i.'|o_id"]\').val();
														if (valo.length == 1) {
															var valo = 0 + valo;
														}
														
														$(\'input[name="repertoire|'.$i.'|rep_location"]\').val("/RepSeq/RS_Data/" +
														dossier + "/" +
														$(\'input[name="repertoire|'.$i.'|run_id"]\').val() +"/TSV/collapsed/"+
														$(\'input[name="repertoire|'.$i.'|p_id"]\').val() +"_"+ 
														valo +"_"+ 
														$(\'input[name="repertoire|'.$i.'|s_id"]\').val() +"_QC.tsv");
														/* +"_"+ 
														$(\'input[name="repertoire|'.$i.'|a_id"]\').val() +"_"+ 
														$(\'input[name="repertoire|'.$i.'|m_id"]\').val());*/
												}
											});
										</script>';
								}
								if ($choice == 0) {
									for ($i = 0; $i < $countrow; $i++) {
										echo '<script>
											$(\'input[name="repertoire|'.$i.'|rep_excluded"]\').val(false);
										</script>';
									}
								}
								if ($choice == 1) {
									for ($i = 0; $i < $countrow; $i++) {
										echo '<script>
											var today = new Date();
											var d = today.getDate();
											var m = today.getMonth()+1; //January is 0!
											var y = today.getFullYear();
											if(d<10) {
												d="0"+d
											} 
											if(m<10) {
												m="0"+m
											} 
											today = y+"/"+m+"/"+d;
											$(\'input[name="repertoire|'.$i.'|rep_curation_date"]\').val(today);
										</script>';
									}
								}
							}
							else {
								if ($f == 'o_id' || $f == 's_id' || $f == 'a_id' || $f == 'm_id') {
									for ($i = 0; $i < $countrow; $i++) {
											echo '<script>						
												$(\'input[name="repertoire|'.$i.'|'.$f.'"]\').focusout(function() {
													if ($(\'input[name="repertoire|'.$i.'|p_id"]\').val() != "" && 
														$(\'input[name="repertoire|'.$i.'|o_id"]\').val() != "" && 
														$(\'input[name="repertoire|'.$i.'|s_id"]\').val() != "" &&
														$(\'input[name="repertoire|'.$i.'|a_id"]\').val() != "" &&
														$(\'input[name="repertoire|'.$i.'|m_id"]\').val() != "" ) {
															var valo = $(\'input[name="repertoire|'.$i.'|o_id"]\').val();
															if (valo.length == 1) {
																var valo = 0 + valo;
															}
															$(\'input[name="repertoire|'.$i.'|rep_id"]\').val(\'\' + 
															$(\'input[name="repertoire|'.$i.'|p_id"]\').val() +"_"+ 
															valo +"_"+ 
															$(\'input[name="repertoire|'.$i.'|s_id"]\').val() +"_"+ 
															$(\'input[name="repertoire|'.$i.'|a_id"]\').val() +"_"+ 
															$(\'input[name="repertoire|'.$i.'|m_id"]\').val());
													}
												});
											</script>';
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
?>
