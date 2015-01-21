<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Moteur de recherche</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link rel="stylesheet" media="screen" type="text/css" title="Design" href="style2.css" />
<style type="text/css">
    #tagcloud {
        width: 650px;
        height: 250px;
        background:#eeeeee;
        color:#0066FF;
        padding: 10px;
       margin-left: 7%;
        border: 2px  #ccc solid;
        text-align:center;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 10%;
		
    }
</style>
</head>
<body>
     <p align="center">
	<img src="images/image1.png"> 
	<!--<a href="http://localhost/indexation/projet/lire_corpus.php"><button>Indexation</button></a>-->

<?php  
echo"<br><br>"; 
function getTagCloud(){

$min_size = 10;
$max_size = 40;

		$query = "SELECT id_mot, SUM(poids) as number  FROM t_document_mot GROUP BY id_mot ORDER BY SUM(poids) DESC LIMIT 0,50"; 
		$req_poids = mysql_query($query);
		
	

 $min = $max=6;

  while ($tag = mysql_fetch_assoc($req_poids)) {
	if ($tag['number'] < $min) $min = $tag['number'];
	if ($tag['number'] > $max) $max = $tag['number'];
	
	$tag['motid']=$tag['id_mot'];
	$query = "select mot from t_mot where id_mot = ".$tag['motid'];
	$result=mysql_query($query);
	$row =mysql_fetch_assoc($result);
	$tag['mot']= $row['mot'];
	$tags[] = $tag;
}

      foreach ($tags as $tag) {
	$tag['size'] = intval($min_size + (($tag['number'] - $min) * (($max_size - $min_size) / ($max - $min))));
	
	
	$tags_extended[] = $tag;
}

shuffle($tags_extended);

 foreach ($tags_extended as $tag) {
   
	echo "<span style=\"font-size:{$tag['size']}px\">";
	echo "<a href='recherche.php?Mot=".$tag['mot']."&submit=GO' >".$tag['mot']."</a>";
	
	echo "</span> </td>";
 
   }
}
?>

 <!--zone recherche-->
	<div id="form" method="get">
							
		<form action ="" method="GET" >
			<table align="0 auto" >					
				<tr>
					<td>
						<input type="text" name="Mot" size="73"></type>
					</td>
			
					<td>
							<input type="submit" name="submit" value="Recherche"></input>
					</td>
				</tr>
			</table>
		</form>
	</div><br>
	<!--resultat-->
<div id="res" align="center">

<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$bdd = "bddmi";
@mysql_connect($host, $user, $password) or die ("Connexion au serveur impossible");
mysql_select_db($bdd) or die ("Connexion a la base impossible");

if(isset($_GET['Mot'])){ 
	$Mot = $_GET['Mot'];

if ($Mot == "") {
	echo "
	<h3 align='center'><font color='black'>Veuillez entrer un mot...</font></h3>

	";
}
else { 
    $query = "SELECT * FROM t_mot WHERE mot = '$Mot'";
	$result = mysql_query($query);

	//L'exctraction des enregistrements contenant le mot
	$query1= "SELECT count(distinct t_document_mot.id_document) FROM t_document_mot,t_document,t_mot
	WHERE (t_document_mot.id_document = t_document.id) and  (t_document_mot.id_mot = t_mot.id) and mot = '$Mot' ";   
    $result1 = mysql_query($query1)or die ('Erreur : '.mysql_error() );  
	$nb_doc = mysql_fetch_row($result1);
	$total = $nb_doc[0];

	if ($nb_doc=mysql_fetch_object($result)){ 
	 
	$query = "SELECT distinct mot,source,poids FROM t_document_mot,t_document, t_mot
	WHERE (t_document_mot.id_document = t_document.id) and  (t_document_mot.id_mot = t_mot.id) and mot = '$Mot'"; 
	$result = mysql_query($query) or die ('Erreur : '.mysql_error() ); 
    $req_desc = mysql_query("SELECT * from t_document");
	
	$resultat1 = mysql_query($query);
	$ligne=@mysql_fetch_object($resultat1);

	echo "
	<table class='hilite' id='highlight'>
	<thead>
	<tr><th>Total Resultat</th><th>Mot</th><th>Poids</th><th>Source</th></tr>
	</thead>
	<tbody>
	<tr><td>$total</td><td>$ligne->mot</td><td>$ligne->poids</td><td>$ligne->source</td></tr>
	</tbody>
	</table>
	";
	$donnees = array();
	
         while ($donnees = @mysql_fetch_array($resultat1)) {                      
                          $resultat[0] = $donnees['mot'] ;  
						                      
                           //echo genererNuage($resultat);
                          }


	}
	
	else {
	echo "
		<h3 align='center'><font color='black'>Mot introuvable</font></h3>
	";
	}
	
}

}


?>

<br>
<?php
	 function genererNuage( $data = array() , $minFontSize = 10, $maxFontSize = 36 )
	{
		$tab_colors=array("#FFFF00","#FFFACD","#EC1E85","#14E414","#9EA0AB","#9EA414");
		$minimumCount = min( array_values( $data ) );
		$maximumCount = max( array_values( $data ) );
		$spread = $maximumCount - $minimumCount;
		$cloudHTML = '';
		$cloudTags = array();
		$spread == 0 && $spread = 1;
		srand((float)microtime()*1000000);
		$mots = array_keys($data);
		shuffle($mots);
	foreach( $mots as $tag )
	{
		$count = $data[$tag];
		$color=rand(0,count($tab_colors)-1);
		$size = $minFontSize + ( $count - $minimumCount )
		* ( $maxFontSize - $minFontSize ) / $spread;
		$cloudTags[] ='<a style="font-size:28px; '.
		floor( $size ) . 'px' .	'; color:' . $tab_colors[$color]. '; " title="Rechercher le tag ' . $tag . 
                '" href="rechercher.php?q=' . urlencode($tag) . '">' . 	$data[$tag] . 	'</a>';
	}
	return join( "\n",$cloudTags ) . "\n";
}
?>
<div id="tagcloud">
 <?php 
       @mysql_connect('localhost','root','');
	   mysql_select_db('bddmi'); 
      $reponse =  mysql_query("SELECT * FROM t_mot");
        $donnees = array();
		echo "<div style='height:250px; width:650px; overflow: auto; overflow-y: scroll;'>";
         while ($donnees = @mysql_fetch_array($reponse)) {                      
                          $resultat[0] = $donnees['mot'] ; 
						  
						                      
                           echo genererNuage($resultat);
                          }
         echo "</div>";                 
     
        @$mysql_close();
 ?>
<div id="tag" align="center">
<?php
							
	@mysql_connect("127.0.0.1", "root", "") or die ("Connexion au serveur impossible");
	// le choix de la bddmi
	mysql_select_db("bddmi") or die ("Connexion a la base impossible");
	echo '<br>';

?>
</div>


</div>

</div>
</body>
</html>