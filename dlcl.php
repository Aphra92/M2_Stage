<?php
	session_start();
	//Include
	$title="Download CLdb";
	include("includes/start.php");
	include("includes/dblog.php");
?>
<?php
// Creation du menu déroulant
$schema = 'tcr';
//~ $schema = 'tcr';

$listrep = array(
	"repertoire" => "repertoire",
);
		
$requete = "SELECT p_id, e_id, o_id, s_id, a_id, m_id, run_id, rep_id, n_ana FROM ".$schema.".repertoire";
$resultat = $pdo->query($requete) or die(print_r($pdo->errorInfo()));
$replist = array();
$repkey = array();
foreach ($resultat as $row) {
	//p($row);
	//~ $replist[] = $row['rep_id'];
	$replist[] = $row['run_id'].' '.$row['rep_id'].' '.$row['n_ana'];
	$repkey[] = $row['p_id'].','.$row['e_id'].','.$row['o_id'].','.$row['s_id'].','
				.$row['a_id'].','.$row['m_id'].','.$row['run_id'].','.$row['rep_id'].','.$row['n_ana'];
}
//~ p($replist);
$strmenu = " <br><SELECT name='myInputs[]' size='1' class = 'style2'>";
for ($i = 0; $i < sizeof($replist); $i++) {
	$strmenu .= '<OPTION value = \''.$repkey[$i].'\'>'.$replist[$i].'</OPTION>';
	//~ $strmenu .= '<OPTION value = \''.$repkey[$i].'\'>'.$repkey[$i].'</OPTION>';
}
$strmenu .= "</SELECT>";
?>

<!-- script pour ajouter autant de div que demandé par l'utilisateur -->
<script type="text/javascript">
	var counter = 1;
	function addInput(divName){
		  var newdiv = document.createElement('div');
		  newdiv.innerHTML = "<?php echo $strmenu;?>";
		  document.getElementById(divName).appendChild(newdiv);
		  counter++;
	}
</script>

<!-- Affichage du formulaire -->
<form method="POST" action="./includes/function_dlcl.php">
<!-- Type de CL	-->
<div id = "seldlcl">
	<div class ="type">
		<label>DNA</label><input type="checkbox" name="type" value = "DNA" onClick="check('type','DNA');">
		<label>Amino Acid</label><input type="checkbox" name="type" value = "AminoAcid" onClick="check('type','AminoAcid');" checked="checked">
	</div>
	<div class ="coll">
		<label>collapsed</label><input type="checkbox" name="coll" value = "collapsed" onClick="check('coll','collapsed');" checked="checked">
		<label>Not collapsed</label><input type="checkbox" name="coll" value = "notcollapse" onClick="check('coll','notcollapse');">
	</div>
</div>
	
<div id="dynamicInput">
<?php echo $strmenu;?>
</div>
<br>
<input type="button" value="Ajouter un repertoire" onClick="addInput('dynamicInput');" class="head"> <br>
<input type="button" name="Effacer" value="Effacer" class="head" onclick='location.reload();'>
<input type="submit" name="Envoyer" value="Dowload" class="head" ></form>

<script type="text/javascript">
	// Empeche de cocher 2 checkbox sur la meme div
	function check(div, divName) {
		//~ alert(div);
		var div = div;
		var val = divName;
		var checks = document.querySelectorAll('.'+div+' input[type="checkbox"]');
		for(var i = 0; i < checks.length;i++){
			if (checks[i].value != val) {
				checks[i].checked = false;
			}
		}
	}
</script>
    
</body>
<?php
	include('./includes/footer.php');
?>
</html>
