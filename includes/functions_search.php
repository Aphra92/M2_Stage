<?php
// Ces fonctions permette l'affichage du tableau de resutat de la recherche

// Cas ligne de titre gauche. On met a la suite une ligne vide et le nom de colonne qui suit
function add_to_title_gauche_title($k) {
	return '<tr><th class= "res"></th></tr><tr><td class = "titlegauche">'.$k.'</td></tr>';
}

// on crée un td avec le nom de colonne
function add_to_title_gauche($k) {
	return '<tr><td class = "titlegauche">'.$k.'</td></tr>';
}

// On crée un titre avec la bonne class
function create_title($title) {
	return '<tr><th class="res">'.$title.'</th></tr>';
}

// On crée une case contenant la valeur de la colonne
function create_case_key($k, $val, $link) {
	return '<tr>
				<td>
					<a href = "resultats.php?id='.$k.'&amp;val='.$link.'">
						<span class = "link" >'.$val.'</span>
					</a>
				</td>
			</tr>';
}

// On crée une case contenant la valeur de la colonne. Quand on over cette valeur, deux liens apparaisent.
// un lien pour afficher les resultats et un lien pour modifier la ligne dans la db
function create_case_admin($k, $cpt, $val, $link) {
	//~ return '<tr>
				//~ <td class = "tolink_'.$cpt.'" onmouseover="inchoice(\'tolink_'.$cpt.'\')" onmouseout="outchoice(\'tolink_'.$cpt.'\')">
					//~ <a href = "resultats.php?id='.$k.'&amp;val='.$link.'">
						//~ <span class = "link link_'.$cpt.'" >'.$val.'
							//~ <em class="tomodif tomodif_'.$cpt.'"><a href = "resultats.php?id='.$k.'&amp;val='.$link.'">link</a></em>
							//~ <em class="tomodif tomodif_'.$cpt.'"><a href = "modif.php?id='.$k.'&amp;val='.$link.'">modify</a></em>
						//~ </span>
					//~ </a>
				//~ </td>
			//~ </tr>';
	return '<tr>
				<td class = "tolink_'.$cpt.'" onmouseover="inchoice(\'tolink_'.$cpt.'\')" onmouseout="outchoice(\'tolink_'.$cpt.'\')">
					<span class = "link link_'.$cpt.'" >'.$val.'</span>
						<em class="tomodif tomodif_'.$cpt.'"><a href = "resultats.php?id='.$k.'&amp;val='.$link.'">'.$val.'</a></em>
						<em class="tomodif tomodif_'.$cpt.'"><a href = "modif.php?id='.$k.'&amp;val='.$link.'">modify</a></em>
				</td>
			</tr>';
}

function create_case($val) {
	return '<tr><td>'.$val.'</td></tr>';
}
?>
