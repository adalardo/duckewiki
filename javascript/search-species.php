﻿<?php 
session_start();
//Check whether the session variable
if(!isset($_SESSION['userid']) || 
	(trim($_SESSION['userid'])=='')) {
		header("location: access-denied.php");
	exit();
} 

include "../functions/databaseSettings.php";
require_once "../".$relativepathtoroot.$databaseconnection;

$dbname = $_SESSION['dbname'];
$conn = ConectaDB($dbname);
	
	$idtag = strip_tags($_GET['idtag']);
	$idres = strip_tags($_GET['idres']);
	$nomeid = strip_tags($_GET['nomeid']);
?>
<?php

	$searchq		=	strip_tags($_GET['q']);
	$searchq		=	strtolower($searchq);
	$getRecord_sql	=	"SELECT CONCAT(Genero,' ',Especie) as nome,EspecieID  FROM Tax_Especies JOIN Tax_Generos USING(GeneroID) WHERE LOWER(CONCAT(Genero,' ',Especie)) LIKE '%".$searchq."%' AND Especie IS NOT NULL";
	$getRecord	= mysql_query($getRecord_sql,$conn);
	$ngetRecord	= mysql_numrows($getRecord);

	if($ngetRecord>0){
			echo '<ul>';
			while ($row = mysql_fetch_array($getRecord)) {
				$tgn = $row['nome'];
				echo "<li><a href=\"javascript:substitui('".$tgn."','".$idtag."','".$idres."', '".$row['EspecieID']."', '".$nomeid."');\">".$tgn."</a></li>";
			} 	
			echo '</ul>';
	} elseif (strlen($searchq)>0) {
		echo '<ul>';
				echo "<li>".GetLangVar('naoencontrado')."</li>";
			echo '</ul>';
	}
?>