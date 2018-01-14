<?php
	session_start();
	//Include
	$title="Selection add data";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
// Get all column name from the tcr database
$link = "./form_newdata.php";
?>

<script type="text/javascript">
	// autocheck entre 2 checkbox
	// Permet de cocher les checkbox entre 2 box cocher
	/*function check(divName) {
		// recup les checkbox ayant pour id divname
		var checks = document.querySelectorAll('#' + divName + ' input[type="checkbox"]');
		temp = new Array();
		for(var i = 0; i < checks.length;i++){
			var check = checks[i];
			if(check.checked == true) {
				temp.push(i); // stock les numero d'index des checkbox checked
			}
		}
		var maxi = temp.length - 1;
		// Parcours les checkbox et coche entre les checkbox cochés
		for (var i = temp[0]; i <= temp[maxi];i++) {
			var check = checks[i];
			check.checked = true;
		}
	}*/
	
	// Empeche de cocher 2 checkbox sur la meme div
	/*function check(divName) {
		var div = divName.split(',')[0];
		var name = divName.split(',')[1];
		var checks = document.querySelectorAll('#' + div + ' input[type="checkbox"]');
		for(var i = 0; i < checks.length;i++){
			if (checks[i].name != name) {
				checks[i].checked = false;
			}
		}
	}*/
</script>

<h1>Select the table to fill : </h1>

<div id = "data_options">
	<?php
	$names = array(
		"Project" => "Project",
		"Experiment" => "Experiment",
		"Organism" => "Organism",
		"Sample" => "Sample",
		"Action" => "Action",
		"Aliquot" => "Aliquot",
		"Protocole" => "Protocole",
		"Manip" => "Manip",
		"Run" => "Run",
		"Repertoire" => "Repertoire",
	);
	echo '<form action="'.$link.'" method="post">';
	echo '<div id = \'addtoform\'>';
	echo '<TABLE><tr>';
	echo 	'<TD><SELECT id = "choicetable" name = "table" onChange="Choix(\'choicetable\')">';
	foreach ($names as $name) {
	echo '<OPTION value = "'.$name.'">'.$name.'</OPTION>';
	}
	echo '</SELECT></TD>';
	echo '<TD><input type="number" name="quantity" min="1" class="selectnumber"></TD>';
	echo '<TD><input type="submit" value="new data" class="data.send"></TD></tr></TABLE></div></form>';
	?>
</div>

<?php
$exp = '<input type="text" name="p_id" placeholder="p_id" required>'; 
$orga = '<input type="text" name="p_id" placeholder="p_id" required><input type="text" name="e_id" placeholder="e_id" required>'; 
$samp = '<input type="text" name="p_id" placeholder="p_id" required><input type="text" name="e_id" placeholder="e_id" required><input type="text" name="o_id" placeholder="o_id" required>'; 
$aliq = '<input type="text" name="p_id" placeholder="p_id" required><input type="text" name="e_id" placeholder="e_id" required><input type="text" name="o_id" placeholder="o_id" required><input type="text" name="s_id" placeholder="s_id" required>'; 
$man = '<input type="text" name="p_id" placeholder="p_id" required><input type="text" name="e_id" placeholder="e_id" required><input type="text" name="o_id"  placeholder="o_id"required><input type="text" name="s_id" placeholder="s_id" required><input type="text" name="a_id" placeholder="a_id" required>'; 

?>


<script>
function Choix(table) {
	var val = document.getElementById(table).value;
	if (document.getElementById('lots') != null)
		document.getElementById('lots').remove();
	if (document.getElementById('lotslabel') != null)
		document.getElementById('lotslabel').remove();
	if (document.getElementById('prefill') != null)
		document.getElementById('prefill').remove();
	if (val == 'Experiment' 
		|| val == 'Organism'
		|| val == 'Sample'
		|| val == 'Aliquot'
		|| val == 'Manip') {
		//~ alert(val);
		// Ajoute une checkbox a la page et un label
		if (document.getElementById('lots') == null) {
			document.getElementById('addtoform').appendChild(createNewLabel('lots', 'lots ?'));
			document.getElementById('addtoform').appendChild(createNewCheckboxt('lots', 'lots'));
		}
		// Si elle n'est pas coché, pas de case
	}
}

function createNewCheckboxt(name, id){
    var checkbox = document.createElement('input'); 
    checkbox.type= 'checkbox';
    checkbox.name = name;
    checkbox.id = id;
    checkbox.setAttribute("onChange",'return addform();');
    return checkbox;
}

function createNewLabel(id, txt){
    var newlabel = document.createElement("Label");
	newlabel.setAttribute("for",id);
	newlabel.innerHTML = txt;
	newlabel.id = id+'label';
    return newlabel;
}

function addform(){
    var id = document.getElementById('lots');
    var val = document.getElementById('choicetable').value;
    //~ alert(val);
    // Si c'est coché on cré une div et des cases a pre remplir en fonction de val
    if (id.checked == true) {
		var newdiv = document.createElement('prefill');
		newdiv.setAttribute("id", 'prefill');
		newdiv.setAttribute("name", 'prefill');
		if (val == 'Experiment') {
			var form = '<?php echo $exp ?>';
		}
		if (val == 'Organism') {
			var form = '<?php echo $orga ?>';
		}
		if (val == 'Sample') {
			var form = '<?php echo $samp ?>';
		}
		if (val == 'Aliquot') {
			var form = '<?php echo $aliq ?>';
		}
		if (val == 'Manip') {
			var form = '<?php echo $man ?>';
		}
		newdiv.innerHTML = 	form;
		document.getElementById('addtoform').appendChild(newdiv);
	}
	else { // on supprime la div
		if (document.getElementById('prefill') != null) {
			document.getElementById('prefill').remove();
		}
	}
}
</script>

</body>
<?php
	include('./includes/footer.php');
?>
</html>

