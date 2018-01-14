<?php
	session_start();
	//Include
	$title="curation";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
// Stock la variable contenant la base sur laquelle on requete
$schema = 'tcr';
//~ p($_POST);
// Si on vient d'arriver sur la page
if (isset($_POST)) {
	if (sizeof($_POST) == 3) {
		// On supprime un potentiel ancien formulaire
		if (isset($_SESSION['save_form'])) {
			//~ echo '<br> session supprimé <br>';
			//~ p($_SESSION['save_form']);
			unset($_SESSION['save_form']);
		}
		$corres = Array(
			"Action" => "ac_curation",
			"Manip" => "m_curation",
			"Organism" => "o_curation",
			"Sample" => "s_curation",
			"Aliquot" => "a_curation",
			"Repertoire" => "rep_curation",
		);
		if (isset($_POST['quantity'])) {
			if ($_POST['quantity'] == '') 
				$max = 10;
			else 
				$max = $_POST['quantity'];
		}
		if (isset($_POST['select'])) {
			$where = $_POST['select'];
		}
		// On fait la requete pour recupérer tous les non curé dans la db
		foreach (array_keys($_POST) as $select) {
			if ($select != 'quantity' && $select != 'select') {
				if ($select = 'table') 
					$select = $_POST[$select];
				$sel = 'tcr.'.$select;
				/*if ($select == 'Sample')
					$query = 'SELECT * FROM '.$sel.', tcr.action WHERE '.$corres[$select].' '.$where.' 
					AND p_id1 = p_id 
					AND e_id1 = e_id 
					AND o_id1 = o_id 
					LIMIT '.$max;
				else */
					$query = 'SELECT * FROM '.$sel.' WHERE '.$corres[$select].' '.$where.' LIMIT '.$max;
				//~ echo $query;
				$request = $pdo->prepare($query);
				$request -> execute();	//execute request
				$table_rows = array();
				while($donnees = $request->fetch(PDO::FETCH_ASSOC)) {
					$table_rows[] = $donnees;
				}
			}
		}
		//~ p($table_rows);
		// defini le nombre de ligne en fonction du nombre de resultat dans la db
		if (isset($table_rows))
			$countrow = sizeof($table_rows);
		else 
			$countrow = 1;
		
		//keep informations about form
		$save ='';
		foreach (array_keys($_POST) as $val) {
			$save .= $val.',';
		}
		$save .= $countrow;
			
		// Create form	
		// Recup la config du formulaire pour l'ajout de donnée
		$return = config_form($_POST, $pdo, $schema, TRUE);

		// recupere les infos de chaque colonne
		// [colname] => type,length
		$columns_infos = get_info_columns($pdo, $schema);
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
		if (($('input[name="'+cur+'"]').val() == 'OK' ||
			$('input[name="'+cur+'"]').val() == 'Partial' ||
			$('input[name="'+cur+'"]').val() == 'partial' ||
			$('input[name="'+cur+'"]').val() == 'NO' ) &&
			$('input[name="'+curdate+'"]').val() != '' &&
			$('input[name="'+curid+'"]').val() != '') {
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
						//~ alert(data[val]);
					}
				},
				error: function() {
					//~ alert('La requête n\'a pas abouti'); 
				}
			}); 
			$('input[name="'+line+'"]').attr('onclick', '');
		}
		else {
			//~ alert(cur+curdate+curid)
			if ($('input[name="'+cur+'"]').val() == 'OK' ||
				$('input[name="'+cur+'"]').val() == 'Partial' ||
				$('input[name="'+cur+'"]').val() == 'partial' ||
				$('input[name="'+cur+'"]').val() == 'NO')
				$('input[name="'+cur+'"]').css("background","#50D050");
			else 
				$('input[name="'+cur+'"]').css("background","red");
			if ($('input[name="'+curdate+'"]').val() == '')
				$('input[name="'+curdate+'"]').css("background","red");
			if ($('input[name="'+curid+'"]').val() == '')
				$('input[name="'+curid+'"]').css("background","red");
		}
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
