<?php
	session_start();
	//Include
	$title="Recherche rapide";
	include("includes/start.php");
	include("includes/dblog.php");
?>

<?php
$maxparpage=15;

if(isset($_POST["recherche"])) {
	//~ echo 'cas recherche <br>';
	if (isset($_SESSION['tcr'])) {
		unset($_SESSION['tcr']);
		unset($_SESSION['total']);
		unset($_SESSION['fieldname']);
	}
	// htmlspecialchars  permet de protéger un formulaire des intrusions avec du code
    $seq = strtolower(htmlspecialchars($_POST["recherche"]));
    //~ unset($_POST["recherche"]);
    //~ echo $seq;
    // On regarde si c'est une seq dna ou aa
    $case = 1; // 1 == AA 2 == DNA
	$pattern = '/(a|t|c|g)*/';
	preg_match($pattern, $seq, $matches);	
    if (strlen($matches[0]) == strlen($seq)) {
		$case = 2;
	}
    $oldseq = '<tochange>'.$seq.'<tochange>';
    $seq = '%'.$seq.'%';
    // Si AA :
    if ($case == 1) {
		$query = 'select tcr.tcr_id, t.p_id, t.e_id, t.o_id, t.s_id, a_id, m_id, run_id, rep_id, n_ana, s_exp_group2, s_cell_sample,
			tcr_vlabel, tcr_jlabel, tcr_cdr3nt, tcr_cdr3aa, tcr_count
			from tcr.tcr, tcr.tcr_obs as t, tcr.sample as s
			where tcr_cdr3aa ilike :seq and tcr.tcr_id = t.tcr_id
			and t.p_id = s.p_id
			and t.e_id = s.e_id
			and t.o_id = s.o_id
			and t.s_id = s.s_id
			ORDER BY rep_id DESC';
		$request = $pdo->prepare($query);
		$request -> bindValue(':seq',$seq);
		$request -> execute();

		$tcr = array();
		while($row = $request->fetch(PDO::FETCH_ASSOC)) {
			$tcr[] = $row;
        }
        //~ p($tcr);
	}
    // Si DNA :
    if ($case == 2) {
		$query = 'select tcr.tcr_id, t.p_id, t.e_id, t.o_id, t.s_id, a_id, m_id, run_id, rep_id, n_ana, s_exp_group2, s_cell_sample,
			tcr_vlabel,tcr_jlabel, tcr_cdr3nt, tcr_cdr3aa, tcr_count
			from tcr.tcr, tcr.tcr_obs as t, tcr.sample as s
			where tcr_cdr3nt ilike :seq and tcr.tcr_id = t.tcr_id
			and t.p_id = s.p_id
			and t.e_id = s.e_id
			and t.o_id = s.o_id
			and t.s_id = s.s_id
			ORDER BY rep_id DESC';
		$request = $pdo->prepare($query);
		$request -> bindValue(':seq',$seq, PDO::PARAM_STR);
		$request -> execute();
		
		$tcr = array();
		while($row = $request->fetch(PDO::FETCH_ASSOC)) {
			$tcr[] = $row;
        }
        //~ p($tcr);
	}

	$i = 0;
	$_SESSION['fieldname'] = array();
	while ($i < $request->columnCount()) {
		$col = $request->getColumnMeta($i);
		$_SESSION['fieldname'][] = $col['name'];
		$i = $i + 1;
	}
	//~ p($_SESSION['fieldname']);
	$total = $request->rowCount();
	$_SESSION['total'] = $total;
	$_SESSION['tcr'] = $tcr;
	//~ p($_SESSION['tcr']);
} 
else {
	//~ echo 'cas changement de page <br>';
	//~ p($_SESSION['tcr']);
	// On recoit une requete en GET
}
	
if (isset($_SESSION['tcr'])) {
	//~ echo $_SESSION['total'];
	$total = $_SESSION['total'];
    
	if ($total == 0) {
		echo "Désolé! Aucun résultat n'a été trouvé dans la base de données<br>";
		$exit = TRUE;
	}
	else { // On affiche uniquement le nombre d'entrés maximal que l'on a défini
		echo '<h1>Results</h1>';
		$nbpages=ceil($total/$maxparpage);
		//~ echo $nbpages;
		
		// On verifie que la variable $_GET['page'] existe
		if(isset($_GET['page'])) {
			// Si elle existe on récupère la page
			$currentpage=intval($_GET['page']);
			// Si la valeur de $currentpage (le numéro de la page) est plus grande que $nbpages...
			if($currentpage>$nbpages) { 
				$currentpage=$nbpages;
			}
		}
		else {
			$currentpage=1; // La page actuelle est la n°1    
        }
        // On calcul la première entrée à lire    
		$premiereEntree=($currentpage-1)*$maxparpage; 
		
		//~ echo '<br>'.$premiereEntree.'<br>';
        
        // Initialisation du tableau avec le nom des colonnes en balises titres
        echo '<div class="divcenter">';
	echo '<div id ="resulttabdiv">';
        echo '<table class="resultats"><tr>';
        echo '<th></th>';
        foreach ($_SESSION['fieldname'] as $field) {
			echo '<th class="res">' . $field . '</th>';
		}
		echo '</tr>';
        
		// On rempli les colones du tableau avec les valeurs de $_SESSION
		$imax = $premiereEntree+$maxparpage;
		if ($imax > sizeof($_SESSION['tcr'])) {
			$imax = sizeof($_SESSION['tcr']);
		}
		for ($i = $premiereEntree; $i < $imax; $i++) {
			// creation du lien pour *
			echo '<tr><td><a href = "resultats.php?id=*&amp;val=';
			$v = '';
			foreach (array_keys($_SESSION['tcr'][$i]) as $id) {
				$val = $_SESSION['tcr'][$i][$id];
				if ($id != 's_exp_group2' && $id != 's_cell_sample' && $id != 'tcr_vlabel' &&
					$id != 'tcr_jlabel' && $id != 'tcr_cdr3nt' && $id != 'tcr_cdr3aa') {
						$v .= $val.',';
				}
			}
			$v = substr($v, 0, -1);
			echo $v.'"><span class = "link" >*</span></a></td>';
			// remplissage du tableau
			$temp = '';
			$save = '';
			$saveval = '';
			foreach (array_keys($_SESSION['tcr'][$i]) as $id) {
				$val = $_SESSION['tcr'][$i][$id];
				if ($id != 's_exp_group2' && $id != 's_cell_sample' && $id != 'tcr_vlabel' &&
				$id != 'tcr_jlabel' && $id != 'tcr_cdr3nt' && $id != 'tcr_cdr3aa') {
					if ($id != 'tcr_id') {
						if ($temp == '')
							$temp .= $val;
						else 
							$temp .= ','.$val;
					}
					if ($id == 'tcr_id') {
						echo '<td><a href = "resultats.php?id='.$id.'&amp;val='.$val.'">
						<span class = "link" >'.$val.'</span></a></td>';
					}
					else {
						if ($id == 'run_id') {
							echo '<td><a href = "resultats.php?id='.$id.'&amp;val='.$val.'">
							<span class = "link" >'.$val.'</span></a></td>';
						}
						else {
							if ($id != 'rep_id'  && $id != 'n_ana') {
								echo '<td><a href = "resultats.php?id='.$id.'&amp;val='.$temp.'">
								<span class = "link" >'.$val.'</span></a></td>';
							}
							if ($id == 'rep_id') {
								$saveid = $id;
								$saveval = $val;
							}
							if ($id == 'n_ana') {
								echo '<td><a href = "resultats.php?id='.$saveid.'&amp;val='.$temp.'">
								<span class = "link" >'.$saveval.'</span></a></td>';
								echo '<td>'.$val.'</td>';
							}
						}
					}
				}
				else 
					echo '<td>'.$val.'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '</div>';
		echo '<p class ="center">';
		
		$next = $currentpage+1;
		$prev = $currentpage-1;
		$next2 = $currentpage+2;
		$prev2 = $currentpage-2;
        
		if($currentpage != 1){
			echo ' <a class ="fleche" href="recherche_rapide.php?page=1"><span class = "numpage">&lt&lt start</span></a>';
			echo ' <a class ="fleche" href="recherche_rapide.php?page='.$prev.'"><span class = "numpage">previous</span></a>';
		}

		for($i=1; $i<=$nbpages; $i++) {
		   if($i==$currentpage) {
				echo '<span class = "col" > '.$i.' </span>'; 
		   }  
		   elseif($i == $next  OR $i == $prev OR $i == $next2 OR $i == $prev2) {
				echo ' <a href="recherche_rapide.php?page='.$i.'"><span class = "numpage">'.$i.'</span></a>';
		   }
		}

		if($currentpage != $nbpages) {
			echo ' <a class ="fleche" href="recherche_rapide.php?page='.$next.'"><span class = "numpage">next</span></a>';
			echo ' <a class ="fleche" href="recherche_rapide.php?page='.$nbpages.'"><span class = "numpage">end &gt&gt</span></a>';
		}
		echo '</p>';
		echo '</div>';
	}
}
?>

</body>
<?php
	include('./includes/footer.php');
?>
</html>
