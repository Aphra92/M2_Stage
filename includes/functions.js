// La fonction surveille ce qui est coller dans le tableau et permet de remplir automatiquement plusieurs champs
// si l'input contient des /t et \n
/*$('input').bind('paste', null, function(e){	// check paste action
	// define array with name of form input
	var temp = new Array();
	$("#tab").find($("input:text")).each(function(i){ 
		temp.push($(this).attr("name")); // récupère les noms de chaque input
	});		
	
	$('#d').attr('name');
    $this = $(this);
    
    // recupere l'index de l'element et la class de la ou on a coller
    var id = $(this).index('#tab input');
    var cla = $(this).attr("class"); // récupère la class
    var clanb = cla.replace( /^\D+/g, ''); // recupere la valeur entiere de la class
    var cla = cla.replace(/\d+/,'')	// recupere tout sauf l valeur entiere de la class
    
    setTimeout(function() {
        var lines = $this.val().split(/ /);
        $this.val(' ');
        
        //alert(lines.length);
        for (var i = 0; i < lines.length; i++) { // parcours les lignes			
			// on parse les /t pour chaque ligne
			var columns = lines[i].split(/\t/);
			var col = 0; // nombre de colonnes pour la ligne
			if (columns.length == 1) {	// si un seul element, il est coller normalement
				var str = temp[id].split('|');
				var newstr = str[0]+'|'+clanb+'|'+str[2]; // construit un string pour name
				$('input[name="' + newstr + '"][ class="' + cla+clanb + '"]').val(columns[col]);
			}
			// Si plusieurs element
			else {
				for (var j = id; j < columns.length+id; j++)  { 
					var str = temp[j].split('|');
					//alert(str);
					var newstr = str[0]+'|'+clanb+'|'+str[2];
					$('input[name="' + newstr + '"][ class="' + cla+clanb + '"]').val(columns[col]);
					col++;
				}
			}
			// Augmente de 1 la class
			clanb++;
		}
    }, 0);	
});*/



