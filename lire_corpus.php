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
include 'indexation.php';
//Augmentation du temps
//d'exécution de ce script

set_time_limit (500);

$path= "ccm";

explorerDir($path);

function explorerDir ($path)
{
	/*$link = mysql_connect('localhost','root','');
	$sql="Create DATABASE fichh";
	mysql_query($sql,$link);
	mysql_query("
	CREATE TABLE IF NOT EXISTS document (
  id_document int(11) NOT NULL AUTO_INCREMENT,
  source varchar(255) NOT NULL,
  texte_titre mediumtext NOT NULL,
  texte_description mediumtext NOT NULL,
  PRIMARY KEY (id_document)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;",$link);
*/
	$folder = opendir($path);
	while ($entree = readdir($folder))
	{
		//on ignore les entrées . . .
		if ($entree != "."&& $entree !="..")
		{
			//on verifie si il s'agit d'un répertoire
			if (is_dir($path."/".$entree))
			{
				$sav_path = $path;
				//construction du path jusqu'au nouveau répertoir
				$path.= "/".$entree;
				echo "DOSSIER =" , $path, "<BR>";
				// on parcours le nouveau répertoir
				explorerDir($path);
				$path = $sav_path;	
			}
			else
			{
				//c'est un fichier html ou pas
				 $path_source = $path."/".$entree;
				//si c'est un html
				if( strpos($path_source,".htm")>0)
				{				
					//on appel la fonction d'indexation
					//dans le module indexation.php
					//par un include
					echo "ss=".$path_source,"<br>";
					indexer($path_source);
				}
			}
		}
	}
}
?>
