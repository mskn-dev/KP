<?php
//appel aux fonctions
include 'bibiliotheque.inc.php';

//$fichier = "source.html";

//indexer($fichier);

function indexer($fichier)
{
	////////////////////////////////////////////////
	//SOURCE DE TRAITEMENT
	////////////////////////////////////////////////

	//Mise du contenu du fichier sours forme
	//d'un seul texte
	$tab_html = file($fichier);
	$texte_html = implode($tab_html, " ");

	////////////////////////////////////////////////
	//Traitement HEAD
	////////////////////////////////////////////////
	//Récupération titre
	$titre = get_title($texte_html);
	echo "t=".$titre;

	//Récupération métas
	
	$meta_keywords = get_keywords($fichier);
	//echo "</br>meta_key $meta_keywords";
	$meta_description = get_description($fichier);
	//echo "metades=".$meta_description;
	//1: Récupération du texte head
	$texte_head = $titre . " " . $meta_keywords . " " . $meta_description;
	
	//Processus d'indexation du texte head
	//2: Mise en miniscule
	$texte_head = strtolower($texte_head);
	
	//3: Fragmenter le texte_head
	//   Filtrer la ponctuation et autres caractères 
	//4: Filtrer les élements par taille <= 2
	$separateurs = " ,.():!?»«\t\"\n\r\'-+/*%{}[]";
	$tab_elems_head = fractionner_chaine($separateurs, $texte_head);
	//echo "<br/> textehead $texte_head";
	//echo "<br/> 0 textehead $tab_elems_head[0]";
	//echo "<br/> 1 textehead $tab_elems_head[1]";
	//echo "<br/> 3 textehead $tab_elems_head[3]";
	//echo "<br/> 4 textehead $tab_elems_head[4]";
	//5: Filtrer par liste de mots-vides : Pas encore!
	//Pas encores Pas encore

	//5: Calculer les occurrences
	$tab_elems_occurrences_head = array_count_values($tab_elems_head);

	//7: Passer d'occurrences au poids :  coefficient = 1 = body
	$coefficient = 3;
	$tab_elems_poids_head = passer_occurrences_poids($tab_elems_occurrences_head, $coefficient);
	//echo "<br/> 0 textehead $tab_elems_head[0] $$tab_elems_poids_head[0]";
	//echo "<br/> 1 textehead $tab_elems_head[1] $$tab_elems_poids_head[1]";
	//echo "<br/> 3 textehead $tab_elems_head[3] $$tab_elems_poids_head[3]";
	//echo "<br/> 4 textehead $tab_elems_head[4] $$tab_elems_poids_head[4]";
	/*
	//7: Afficher la liste des mots <--> occurrences
	foreach($tab_elems_poids_head as $elem => $poids)
	{
		echo $fichier, " ----> ", $elem, " -----> ", $poids, "<br />";
	}
	*/

	echo "<hr>";
	
	////////////////////////////////////////////////
	//Traitement BODY
	////////////////////////////////////////////////
	//1: Recupérer le body : <body> | ... | </boy>
	$html_body = get_body($texte_html);
	
	//2: Convertir les entités html aux caracts
	$html_body_sans_entites = convertirEntitesCaracts($html_body);
	
	//3: Supprimer les balises html -> texte : débalisage
	// ICI J'AI LE TEXTE (BODY)
	$texte_body = strip_tags($html_body_sans_entites);
	
	//Processus d'indexation du texte
	//1: Mise en miniscule
	$texte_body = strtolower($texte_body);
	
	//remplacement du descriptif meta par le premier passage body si taille <100
	if (strlen($meta_description) < 100 ) $meta_description = substr($texte_body, 0, 250);
	//else $descriptif = $meta_descriptif;
	
	//2: Fragmenter le texte_head
	//   Filtrer la ponctuation et autres caractères
	//3: Filtrer les élements par taille <= 2
	$separateurs = " ,.():!?»«\t\"\n\r\'-+/*%{}[]";
	$tab_elems_body = fractionner_chaine($separateurs, $texte_body);

	//4: Filtrer par liste de mots-vides : Pas encore!
	//Pas encores Pas encore

	//6: Calculer les occurrences
	$tab_elems_occurrences_body = array_count_values($tab_elems_body);

	//7: Passer d'occurrences au poids :  coefficient = 1 = body
	$coefficient = 1;
	$tab_elems_poids_body = passer_occurrences_poids($tab_elems_occurrences_body, $coefficient);

	
	//7: Afficher la liste des mots <--> occurrences
	/*foreach($tab_elems_poids_body as $elem => $poids)
	{
		echo $fichier, " ----> ", $elem, " -----> ", $poids, "<br />";
	}
	*/

	////////////////////////////////////////////////
	//FUSIONNER LES TABs HEAD et BODY ===>  TAB BOCUMENT
	////////////////////////////////////////////////
	$tab_mots_poids_document = fusionner_tab_H_B($tab_elems_poids_head, $tab_elems_poids_body);

	echo "<hr>";
   /*
	// Afficher la liste des mots <--> poids dans le document
	foreach($tab_elems_poids_body as $elem => $poids)
	{
		echo $fichier, " ----> ", $elem, " -----> ", $poids, "<br />";
	}
	*/
	//ICI ON A : le nom de la source, son titre, son descriptif, la liste des mots/poids
	echo "<p>";
	echo "TITRE : $titre", "<br>";
	echo "DESCRIPTIF : $meta_description ",  "<br>";
	echo "NBR MOTS : ", count($tab_mots_poids_document);
	echo "</p>";
	
	////////////////////////////////////////////////
	//Mise en base de données
	////////////////////////////////////////////////
	
	//La mise en base de données des élements, mot, document_mot
		
	$connect= @mysql_connect("localhost","root","");
	$select= @mysql_select_db("bddmi",$connect);
	
	//mettre les données du document dans la table document: tite, le descriptif et le nom
	//$sql= "insert into document(id_document,source,texte_titre, texte_description) values('','$fichier','$titre', '$meta_description' )";
	$a=str_replace("'","''",$fichier);
	$b=str_replace("'","''",$titre);
	$c=str_replace("'","''",$meta_description);
	$sql= "insert into t_document(titre,description,source) values('$b','$c','$a')";	
	@mysql_query($sql);
	$id_document = @mysql_insert_id();
	
	
	// Mettre la liste des mots/poids dans la table mot et la table de relation : document_mot
	foreach($tab_mots_poids_document as $mot=> $poids)
	{
		//Insertion du mot dans la table mot
		//Verification de l'existance du mot dans la table : si le mot existe on recupère //son id, sinon on l'insert
		 $tom = inverse_mot($mot);
		 
		$sql = "select * from mot where mot = '$mot'";
		$resultat = @mysql_query($sql);
		// test si le mot est déja existant
		if (@mysql_num_rows($resultat))
		{
			$tab_mot = @mysql_fetch_row ($resultat);
			$id_mot = $tab_mot[0];
		}
		else
		{	
			$sql= "insert into t_mot(mot) values ('$mot')";
			@mysql_query($sql);
			$id_mot = @mysql_insert_id();
		}
		
		//Jointure document_mot
		$sql= "insert into t_document_mot(id_mot, id_document, poids) values ($id_mot, $id_document, $poids )";
		@mysql_query($sql);
		
	}

}
?>


