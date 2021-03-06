//Système d'onglet pour le profil Joueur
function changeOnglet(i){
	var j;
	for (j=1; j<5; j++){
		document.getElementById('account'+j).style.display = 'none';
	}
	document.getElementById('account'+i).style.display = 'block';
}

function displaySide(i){
	if(i=='droite'){
		j = document.getElementById('sides').offsetLeft;
		if(j > '-800'){
			jleft = j-1080;
			document.getElementById('sides').style.left = jleft+'px';
			 $('sides').toggleClass('sides_change');  
		}
	}
	if(i=='gauche'){
		j = document.getElementById('sides').offsetLeft;
		if(j < '300'){
			jleft = j+280;
			document.getElementById('sides').style.left = jleft+'px';
		}
	}
	displayArrow();
	
}

function displayArrow(){
		j = document.getElementById('sides').offsetLeft;
		if(j > '-1000' && j < '-900'){
			document.getElementById('fleche-droite-vide').style.visibility = 'hidden';
		}else{
			document.getElementById('fleche-droite-vide').style.visibility = 'visible';
		}
		if(j < '400' && j > '300'){
			document.getElementById('fleche-gauche-vide').style.visibility = 'hidden';
		}else{
			document.getElementById('fleche-gauche-vide').style.visibility = 'visible';
		}
	}

$(document).ready(function(){ 
	changeOnglet('1');
	
	/*Sélection des cartes dans le profil*/
	$( ".carte #selectable" ).selectable();
	
	displayArrow();

});


           

