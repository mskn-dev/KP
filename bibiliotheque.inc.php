<?php
//script des fonctions

//Fusionner les tab_head_elems_poids et
//tab_body_elems_poids
function fusionner_tab_H_B($tab_head, $tab_body)
{
	//Parcourir liste HEAD, souvent plus petite
	foreach ($tab_head  as $mot => $poids)
	{
		
		//if($tab_body[$mot]) $tab_body[$mot] = $tab_body[$mot] + $poids;
		if(array_key_exists($mot,$tab_body)) $tab_body[$mot] = $tab_body[$mot] + $poids;
		else $tab_body[$mot] = $poids;
	}
	//C'est le tbaleau global et non le body
	return $tab_body;
}

//Passer d'occurences ===> poids/valeurs
function passer_occurrences_poids($tab_elems, $coefficient)
{
	foreach ($tab_elems as  $mot => $occurences)
	{
		$tab_elems[$mot] = $occurences * $coefficient;
	}
	return $tab_elems;
}

//Récupérer l'entre <body> ... </body>
function get_body($html)
{
	$modele = "/<body[^>]*>(.*)<\/body>/s";
	preg_match($modele, $html, $matches);
	//Retourne l'entre <body>...</boy>
	return $matches[1];
}

//Convertir entités html aux caractères
function convertirEntitesCaracts($html)
{
	// retourne la table de traduction des entités utilisée en interne pa la htmlentities()
	$table_caracts_html = get_html_translation_table(HTML_ENTITIES);

	// retourne un tableau dont les clés sont les valeurs du précédent //$table_caracts_html, et les valeurs sont les clés.  
	$tableau_html_caracts =  array_flip ( $table_caracts_html );

	// retourne une chaine de caractères après avoir remplacer avoir remplacé les éléments/clés par les éléments/valeurs  du tableau associatif de paires  $tableau_html_caracts dans la chaîne $chaine. 
	$html_sans_entites  =  strtr ($html,   $tableau_html_caracts );
	  
	return $html_sans_entites;
}

//Récupération du title de la source
function get_title($html)
{
	$modele = "#\<title\>(.*)\</title\>#i";
	preg_match($modele, $html, $matches);
	return $matches[1];
}

//Récupération du meta description de la source
function get_description($fichier)
{
	$tab_meta = get_meta_tags($fichier);
	if( isset($tab_metas['description']) ) return $tab_metas['description'];
	else return "";
	//return @$tab_meta['description'] or die(mysql_error());
	//return $tab_meta['description'];
}


//Récupération du meta keywords de la source
function get_keywords($fichier)
{
	$tab_meta = get_meta_tags($fichier);
	return $tab_meta['keywords'];
}

//Fonction de découpage d'une chaine
//sur la base de séparateurs
function fractionner_chaine($separateurs, $chaine)
{
	$tab_elem = array();
	$elem = strtok($chaine, $separateurs);
	while($elem != false)
	{
		if(strlen($elem) > 2) $tab_elems[] = $elem;
		$elem = strtok($separateurs);
	}
	return $tab_elems;
}

 //inverser un mot par odre alphabétique
   function inverse_mot($str)
   {   
   //$mot='machin';
   $entry=array();
   $length=strlen($str);
   $i=0;
   for($i=0;$i!=$length;$i++)
   {
   $entry[]=substr($str,$i,1);
   //echo "$i : $entry[$i]<br>";
   }
   
   
   $str = $entry;
   $mot = sort($str);
   foreach ($str as $key => $val) 
  {
   //echo " mot[" . $key . "] = " . $val . "<br>";
   
   $mot = implode($str);
  }
  
  return($mot);
  }

?>