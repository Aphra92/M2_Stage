<?php
	session_start();
	//Include
	$title="Rechercher";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
// Creation du menu déroulant
$schema = 'tcr';

// liste des tables requetables
$listtable1 = array(
	"project" => "project",
	"experiment" => "experiment",
	"organism" => "organism",
	"sample" => "sample",
	"aliquot" => "aliquot",
	"manip" => "manip",
	"run" => "run",
	"repertoire" => "repertoire",
	"stat" => "stat",
	"tcr" => "tcr",
	"tcr_obs" => "tcr_obs",
	"protocole" => "protocole",
	"participe_ac" => "participe_ac",
	"participe_m" => "participe_m",
	"participe_proj" => "participe_proj",
	"participe_s" => "participe_s",
	"participe_a" => "participe_a",
	"entity" => "entity",
	"action" => "action",
);
$templisttable1 = json_encode($listtable1);

// recupération de la liste des colonnes pour chaque table		
$menu = array();
$listcol = array();
foreach ($listtable1 as $table) {
	//~ $menu[$table] = $table;
	$columns_names = get_columns_names($pdo, $schema, $table);
	$menu = concatarray($menu, $columns_names);
}
//~ p($menu);
foreach (array_keys($menu) as $table) {
	//~ p($menu[$table]);
	$listcol[$table] = '';
	foreach ($menu[$table] as $val) {
		if ($listcol[$table] == '') {
			$listcol[$table][] = $val;
		}
		else {
			$listcol[$table][] = $val;
		}
	}
}
//~ p($listcol);
$listcol = json_encode($listcol);

// menu déroulant choix de la table
$menutable = '';
$partmenutable = '';
$menutable .= "<SELECT name = \"table_1\" id = \"table_1\" onChange=\"Choix('table_1')\">";
$menutable .= "<OPTION >--- Choisissez une table ---</OPTION>";
$partmenutable .= "<OPTION >--- Choisissez une table ---</OPTION>";
foreach ($listtable1 as $name) {
	$menutable .=  "<OPTION value = '".$name."'>".$name."</OPTION>";
	$partmenutable .=  "<OPTION value = '".$name."'>".$name."</OPTION>";
}
$menutable .=  "</SELECT>";
$partmenutable .=  "</SELECT>";

// menu deroulant choix de la colonne
$menucolonne = '';
$partmenucolonne = '';
$menucolonne .= "<SELECT name = \"colonne_1\" id = \"colonne_1\">";
$menucolonne .= "<OPTION >--- Choisissez une colonne ---</OPTION>";
$partmenucolonne .= "<OPTION >--- Choisissez une colonne ---</OPTION>";
$menucolonne .= "</SELECT>";
$partmenucolonne .= "</SELECT>";

// menu deroulant selection
$menusel = '';
$partmenusel = '';
$menusel .= "<SELECT name = \"sel_1\" id = \"sel_1\">";
$menusel .= "<OPTION> AND </OPTION>";
$partmenusel .= "<OPTION > AND </OPTION>";
$menusel .= "<OPTION > OR </OPTION>";
$partmenusel .= "<OPTION > OR </OPTION>";
$menusel .= "</SELECT>";
$partmenusel .= "</SELECT>";

// menu deroulant type
$choice = '';
$partchoice = '';
$choice .= "<SELECT name = \"choice_1\" id = \"choice_1\">";
$choice .= "<OPTION value = \"=\"> = </OPTION><OPTION value = \"!=\"> != </OPTION>
			<OPTION value = \">\"> &gt; </OPTION><OPTION value = \"<\"> &lt; </OPTION>
			<OPTION value = \">=\"> &gt;= </OPTION><OPTION value = \"<=\"> &lt;= </OPTION>
			<OPTION value = 'like'> like </OPTION>
			<OPTION value = 'is null'> is null </OPTION><OPTION value = 'is not null'> is not null </OPTION>
			<OPTION value = '*'> * </OPTION>
			</SELECT>";
$partchoice .= "<OPTION value = '='> = </OPTION><OPTION value = '!='> != </OPTION>";
$partchoice .= "<OPTION value = '>'> &gt; </OPTION><OPTION value = '<'> &lt; </OPTION>";
$partchoice .= "<OPTION value = '>='> &gt;= </OPTION><OPTION value = '<='> &lt;= </OPTION>";
$partchoice .= "<OPTION value = 'like'> like </OPTION>";
$partchoice .= "<OPTION value = 'is null'> is null </OPTION><OPTION value = 'is not null'> is not null </OPTION>";
$partchoice .= "<OPTION value = '*'> * </OPTION>";
$partchoice .= "</SELECT>";
			
// menu valeur
$valeur = "<input type=\"text\" name=\"value_1\" id=\"value_1\" onClick=\"auto('value_1')\"><br>";

//~ echo $templisttable1;
//~ echo $listcol;
?>

<script>
	var counter = 1;
	var lastchild = new Array(); // sauvegarde la derniere div pour la supprimer si besoin
	var tab = <?php echo $templisttable1 ?>;
	
	// Ajout d'une ligne du formulaire
	function addform(divName, newdiv, source) {
		if (source == 0) {
			var test = '<?php echo "<input type=\"text\" name = \"bloc_"?>'+counter+'<?php echo "\" id = \"bloc_"?>'+
			counter+'<?php echo "\" style=\"visibility: hidden;\" ><br>";?>';
			var firstlinesel = '';
			var partmenusel = '';
		}
		else {
			var test = '';
			var firstlinesel = '<?php echo "<SELECT name=\"sel_"?>'+
			counter+'<?php echo "\"id=\"sel_"?>'+counter+'<?php echo "\">";?>';
			var partmenusel = "<?php echo $partmenusel; ?>";
		}
		var firstlinetable = '<?php echo "<SELECT name = \"table_"?>'+counter+'<?php echo "\" id = \"table_"?>'+
		counter+'<?php echo "\" onChange=\"Choix(\'table_"?>'+counter+'<?php echo"\')\">";?>';
		var firstlinecolonne = '<?php echo "<SELECT name=\"colonne_"?>'+counter+'<?php echo "\"id=\"colonne_"?>'+
		counter+'<?php echo "\">";?>';
		var firstlinechoice = '<?php echo "<SELECT name = \"choice_"?>'+counter+'<?php echo "\" id = \"choice_"?>'+
		counter+'<?php echo "\">;";?>';
		var firstlinevaleur = '<?php echo "<input type=\"text\" name = \"value_"?>'+counter+'<?php echo "\" id = \"value_"?>'+
		counter+'<?php echo "\" onClick=\"auto(\'value_"?>'+counter+'<?php echo"\')\"><br>";?>';
		newdiv.innerHTML = 	test+firstlinesel+partmenusel+firstlinetable+"<?php echo $partmenutable; ?>"+
			firstlinecolonne+"<?php echo $partmenucolonne; ?>"+
			firstlinechoice+"<?php echo $partchoice;?>"+firstlinevaleur;
		document.getElementById(divName).appendChild(newdiv);
		lastchild.push(newdiv);
	}	
	
	function addcont(divName) {
		var source = 1;
		counter++;
		var newdiv = document.createElement('DIV_'+counter);
		newdiv.setAttribute("id", 'DIV_'+counter);
		newdiv.setAttribute("name", 'DIV_'+counter);
		addform(divName, newdiv, source);
		///////
		checkpos();
		allowed();
	}
	
	function addbloc(divName) {
		var source = 0;
		counter++; 
		var newdiv = document.createElement('BLOC_'+counter);
		newdiv.setAttribute("id", 'BLOC_'+counter);
		newdiv.setAttribute("name", 'BLOC_'+counter);
		addform(divName, newdiv, source);
		///////
		checkpos();
		allowed();
	}
	
	// function pour bien tout remplir
	function checkpos() {
		var flagsimple = false;
		var flagcombine = false;
		var flagallempty = true;
		var flagbloc = false; 
		var val = '';
		for (var i = 1; i <= counter; i++) {
			val = $('#table_'+i+' :selected').text();
			// on regarde si on est dans le cas combine
			if (val == "project" || val == "experiment"
				|| val == "organism" || val == "sample" || val == "aliquot"
				|| val == "manip" || val == "run" || val == "repertoire" || val == "stat" || val == "tcr" || val == "tcr_obs" ) {
					//~ alert(val);
				flagcombine = true;
				flagallempty = false;
				if (val == "tcr" || val == "tcr_obs") {
					flagbloc = true; 
				}
			}
			// on regarde si on est dans le cas simple
			if (val == "protocole" || val == "participe_ac"
			|| val == "participe_m" || val == "participe_proj" || val == "participe_s"
			|| val == "participe_a" || val == "entity" || val == "action") {
				flagsimple = true;
				flagallempty = false;
				break;
			}
		}
		// on parcours tout 
		for (var i = 1; i <= counter; i++) {
			for (value in tab) {
				if (flagsimple) {
					if (value != val)
						$('#table_'+i+' option[value="'+value+'"]').remove();
				}
				if (flagcombine) {
					if (value == "protocole" || value == "participe_ac"
						|| value == "participe_m" || value == "participe_proj" 
						|| value == "participe_s" || value == "participe_a" 
						|| value == "entity" || value == "action" ) {
						$('#table_'+i+' option[value="'+value+'"]').remove();
					}
				}
				if (flagallempty) {
					$('#table_'+i+' option[value="'+value+'"]').remove();
				}
			}
		}
		if (flagallempty) {
			//~ alert('reset');
			for (var i = 1; i <= counter; i++) {
				for (value in tab) {
					$('#table_'+i).append(new Option(value,value));
				}
			}
		}
		if (flagsimple) {
			document.getElementById("buttunIN").disabled = true; 
			flagbloc = true; 
		}
		else {
			document.getElementById("buttunIN").disabled = false; 
		}
		if (flagbloc) {
			document.getElementById("buttunIN").disabled = true; 
		}
		else {
			document.getElementById("buttunIN").disabled = false; 
		}
	}
	
	function allowed() {
		var val = '';
		var forbid = new Array();
		var lastbloc = 'BLOC_1';
		forbid[lastbloc] = new Array();	
		// liste interdite pour les nouveaux blocs
		listforbid = new Array();
		listforbid.push('tcr');	
		listforbid.push('tcr_obs');	
		var bloc = false;
		
		for (var i = 1; i <= counter; i++) {
			var val = $('#table_'+i+' :selected').text();
			if ($('#BLOC_'+i).length > 0) {
				bloc = true;
				lastbloc = 'BLOC_'+i;
				forbid[lastbloc] = new Array();
				//~ alert(lastbloc);
				if (i != counter) {
					forbid[lastbloc].push(val);
					listforbid.push(val);
				}
			}
			if ($('#DIV_'+i).length > 0) {
				bloc = false;
				current = 'DIV_'+i;
				//~ alert(current);
				if (i != counter && lastbloc == 'BLOC_1') {
					forbid[lastbloc].push(val);
					listforbid.push(val);
				}
			}
			if (lastbloc != 'BLOC_1') {
				if (forbid[lastbloc] != '') {
					for (value in tab) {
						if (value != forbid[lastbloc])
							$('#table_'+i+' option[value="'+value+'"]').remove();
					}
				}
				else {
					for (value in listforbid) {
						$('#table_'+i+' option[value="'+listforbid[value]+'"]').remove();
					}
				}
			}
		}
		//~ alert (listforbid);
		//~ for (val in forbid) {
			//~ alert(val);
			//~ alert(forbid[val]);
		//~ }
	}
	
	// Fonction pour effacer la derniere ligne
	function removeline(divName) {
		if (counter > 1) {
			if ($('.IMBRIQ_'+counter).length > 0){
				listforbid = new Array();
			}
			document.getElementById(divName).removeChild(lastchild[lastchild.length - 1]);
			lastchild.splice(-1,1)
			counter--;
		}
	}

<!-- Script pour ajouter dans le formulaire des colonnes celle qui correspondent a la table demandé -->	
function Choix(form) {
	checkpos();
	allowed();
	var col = form.split('_');
	col = col[1];
	//~ alert(form);
	// nom de la table selectionné
	var val = document.getElementById(form).value;
	var json = <?php print_r($listcol) ?>;
	var select = document.getElementById('colonne_'+col);
	
	// Supprime les options deja présentes
	var len = select.options.length;
	for(var i = select.options.length - 1 ; i > 0 ; i--) {
        select.remove(i);
    }
	// Ajoute les options en fonction du tableau
	for (var i in json[val]) {
		var opt = document.createElement('option');
		opt.value = json[val][i];
		opt.innerHTML = json[val][i];
		select.appendChild(opt);
	}
}

// Script d'autocompletion pour chaque case
function auto(form) {
	var col = form.split('_');
	col = col[1];
	var val = document.getElementById(form).id;
	var term = document.getElementById(form).value;
	//~ alert(val);
	//~ alert(term);
	var table = document.getElementById('table_'+col).value;
	var colonne = document.getElementById('colonne_'+col).value;
	//~ alert(table);
	//~ alert(colonne);
	$('#'+val).autocomplete({
		source : function(requete, reponse){
			$.ajax({
				url: 'includes/autocomp_all.php',
				type: 'POST',
				dataType: 'json', 
				data: 	{
							term: requete.term,
							select : colonne,
							from : table,
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
	});
}
</script>

<!-- Affichage du formulaire dans la page -->
<h1>Search</h1>
<form method="POST" action="rechercher_2.php">
<div id="dynamicInput">
	<div id ="DIV_1">
		<?php echo $menutable.' ';?>
		<?php echo $menucolonne.' ';?>
		<?php echo $choice.' ';?>
		<?php echo $valeur;?>
	</div>
</div>
<br>
<input type="button" id = "buttunADD" value="Ajouter une contrainte à la requête" onClick="addcont('dynamicInput');"> <br>
<input type="button" id = "buttunIN" value="Ajouter une jointure à la requête" onClick="addbloc('dynamicInput');"> <br>
<input type="button" name="Reload" value="Reload" class="head" onclick='location.reload();'>
<input type="button" name="Effacer" value="Effacer" onClick="removeline('dynamicInput');">
<input type="submit" name="Envoyer" value="Rechercher">
</form>

</body>
<?php
	include('./includes/footer.php');
?>
</html>
