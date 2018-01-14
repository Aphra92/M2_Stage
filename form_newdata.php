<?php
	session_start();
	//Include
	$title="New data";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
// Stock la variable contenant la base sur laquelle on requete
$schema = 'tcr';
//~ p($_POST);
$prefill = array();
if ((isset($_POST['a_id']))) {
	$prefill['a_id'] = $_POST['a_id'];
	array_pop($_POST);
}
if ((isset($_POST['s_id']))) {
	$prefill['s_id'] = $_POST['s_id'];
	array_pop($_POST);
}
if ((isset($_POST['o_id']))) {
	$prefill['o_id'] = $_POST['o_id'];
	array_pop($_POST);
}
if ((isset($_POST['e_id']))) {
	$prefill['e_id'] = $_POST['e_id'];
	array_pop($_POST);
}
if ((isset($_POST['p_id']))) {
	$prefill['p_id'] = $_POST['p_id'];
	array_pop($_POST);
}
if ((isset($_POST['lots']))) {
	array_pop($_POST);
}
//~ p($prefill);
//~ p($_POST);

// Si on vient d'arriver sur la page
if ((isset($_POST['quantity']))) {
	// On supprime un potentiel ancien formulaire
	if (isset($_SESSION['save_form'])) {
		//~ echo '<br> session supprimé <br>';
		//~ p($_SESSION['save_form']);
		unset($_SESSION['save_form']);
	}
	// defini le nombre de ligne en fonction de la variable quantity
	if ($_POST['quantity'] == 0)
		$countrow = 1;
	else { 
		$countrow = $_POST['quantity'];
	}
	
	//keep informations about form
	$save ='';
	foreach (array_keys($_POST) as $val) {
		if ($val == 'table')
			$val = $_POST[$val];
		$save .= $val.',';
	}
	$save .= $countrow;
	//~ echo $save;
		
	// Create form	
	// Recup la config du formulaire pour l'ajout de donnée
	$return = config_form($_POST, $pdo, $schema, FALSE);

	// recupere les infos de chaque colonne
	// [colname] => type,length
	$columns_infos = get_info_columns($pdo, $schema);
	// Affiche le formulaire sur la page web
	create_form($return["form"], $countrow, $columns_infos, $pdo, $return["foreign"], 
	$return["auto"], $return["opt"], $return["hide"], $return["newkey"], $return["verr"], FALSE, $save);

}
if (isset($prefill)) {
	$emp = $_POST['table'];
	$emp = strtolower($emp);
	if ($emp == 'sample')
		$emp = 'sample,participe_s';
	if ($emp == 'aliquot')
		$emp = 'aliquot,participe_a';
	if ($emp == 'manip')
		$emp = 'manip,participe_m';
	for ($i = 0; $i < $countrow; $i++) {
		echo "<script>
			var prefill = ".json_encode($prefill).";
			for (var prop in prefill) {
				//~ alert(prefill[prop]);
				$(\"input[name='".$emp."|".$i."|\"+prop+\"']\").val(prefill[prop]);
			}
		</script>";
	}
}
if ((isset($_SESSION['save_form']))) {
	//~ echo '<br> Variable session <br>';
	//~ p($_SESSION['save_form']);
	// Sauvegarde ce qui a servi a faire le formulaire
	$save = $_SESSION['save_form']['save'];
	 
	$temp = $_SESSION['save_form'];
	array_pop($_SESSION['save_form']);
	// Garde les valeurs entré précédement dans le formulaire
	$oldvalue = $_SESSION['save_form'];
	// remet $_SESSION tel qu'il etait
	$_SESSION['save_form'] = $temp;
	
	$newPOST = explode(",",$save);
	$countrow = array_pop($newPOST);	// recupere le nombre de ligne
	$new = Array();
	foreach($newPOST as $val){	//transfrome le tableau (infos en clé)
		$new[$val] = $val;
	}
	
	// Create form	
	// Recup la config du formulaire pour l'ajout de donnée
	$return = config_form($new, $pdo, $schema, FALSE);

	// recupere les infos de chaque colonne
	// [colname] => type,length
	$columns_infos = get_info_columns($pdo, $schema);
	// Affiche le formulaire sur la page web
	create_form($return["form"], $countrow, $columns_infos, $pdo, $return["foreign"], 
	$return["auto"], $return["opt"], $return["hide"], $return["newkey"], $return["verr"], FALSE, $save);
	
	// rempli le formulaire
	for ($i = 0; $i < $countrow; $i++) {
		echo "<script>
			var oldvalue = ".json_encode($oldvalue).";
			var count = '.$countrow.';
			for (var prop in oldvalue) {
				//alert(oldvalue[prop]);
				$(\"input[name='\"+prop+\"']\").val(oldvalue[prop]);
			}
		</script>";
	}
}
?>

<script>
  $( function() {
    $( document ).tooltip();
  } );
</script>

<div id="message"></div>

<script>
/*  $(function() {
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
