<?php
	session_start();
	//Include
	$title="Selection curation";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
// Get all column name from the tcr database
$link = "./form_curation.php";
if (!isset($_SESSION['id']) || $_SESSION['id'] <= 1)
	header("Location: ./index.php");
?>

<script type="text/javascript">
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

<h1>Select the table to cure : </h1>

<div id = "data_options">
	<?php
	$names = array(
		"Organism" => "Organism",
		"Sample" => "Sample",
		"Action" => "Action",
		"Aliquot" => "Aliquot",
		"Manip" => "Manip",
		"Repertoire" => "Repertoire",
	);
	echo '<form action="'.$link.'" method="post">';
	echo '<TABLE><tr>';
	echo 	'<TD><SELECT name = "table">';
	foreach ($names as $name) {
	echo '<OPTION value = "'.$name.'">'.$name.'</OPTION>';
	}
	echo '</SELECT></TD>';
	echo '<TD><input type="number" name="quantity" min="1" max="20" class="selectnumber"></TD>';
	echo 	'<TD><SELECT name = "select">
				<OPTION value = "= \'partial\'">partial</OPTION>
				<OPTION value = "= \'ignored\'">ignored</OPTION>
				<OPTION value = "is null" selected = "selected">pending</OPTION>
				</SELECT>
			 </TD>';
	echo '<TD><input type="submit" value="Curation" class="data.send"></TD></tr></TABLE></form>';
	?>
</div>

</body>
<?php
	include('./includes/footer.php');
?>
</html>	
