<html>
<head>
</head>
<body>
<P>
<font color="red">
<B>DEBUT DU PROCESSUS :</B>
</font>
<BR>
<?php echo "Le ", date("d:m:y")," à ",date ("h:i:s"); ?>
</P>
<table border="1" >

<?php
//echo path_source = $path."/".$entree,"<br>";
//indexer($path_source);

//include du module indexation : indexerHTML($sourcehtml)
require_once("indexation.php");

//Augmentation du temps
//d'exécution de ce script
set_time_limit (1000);

$path= "ccm"; //le premier dossier
explorer_corpus($path);
function explorer_corpus($path) //elle prends un chemin
{
	$folder = opendir($path); //ouvrire le dossier CCM
	while($entree = readdir($folder)) //lire le dossier 
{
//On ignore les entrées . ..(il doit trouvé un fichier)

if($entree != "." && $entree != "..")
{
// On vérifie si il s'agit d'un répertoire
if(is_dir($path."/".$entree))
{
$sav_path = $path;
// Construction du path jusqu'au nouveau répertoire
$path .= "/".$entree;
//echo "DOSSIER = ", $path, "<BR>";
// On parcours le nouveau répertoire
explorer_corpus($path);
$path = $sav_path;


}
else //dans le cas de fichier et non pas de dossier
{
//C'est un fichier html ou pas

echo "<font color=\"red\">";
echo $path_source =$path."/".$entree,"<br>"; //affiche le nom de fichier
echo "</font>";

//Si c'est un .html
if(@eregi('.htm', $entree)) //htm est une sous chaine de html donc s'il trouve htm il trouvera html
{
//On appelle la fonction d'indexation
//Dans le fichier index8.php
//Par un include

indexer($path_source);

}
}
}
}

closedir($folder); //fermer le sous dossier et passer au dossier suivant
}
?>
</table>
<P>
<font color="red">
<B>FIN DU PROCESSUS :</B>
</font>
<BR>
<?PHP echo "Le ", date("d:m:y")," à ", date ("h:i:s"); ?>
</P>
</body>
</html>
