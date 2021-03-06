<?php
session_start();
//INCLUI FUNCOES PHP E VARIAVEIS
include "functions/HeaderFooter.php";
include "functions/MyPhpFunctions.php";

//FAZ A CONEXAO COM O BANCO DE DADOS
$lang = $_SESSION['lang'];
$dbname = $_SESSION['dbname'];
$conn = ConectaDB($dbname);

//CHECA SE O USUARIO TEM PERMISSAO
$uuid = cleanQuery($_SESSION['userid'],$conn);
if(!isset($uuid) || 
	(trim($uuid)=='')) {
		header("location: access-denied.php");
	exit();
} else {
	$acclevel = $_SESSION['accesslevel'];
}

//////PEGA E LIMPA VARIAVEIS
$ppost = cleangetpost($_POST,$conn);
@extract($ppost);
$arval = $ppost;

$gget = cleangetpost($_GET,$conn);
@extract($gget);

//CABECALHO
if ($ispopup==1) {
	$menu = FALSE;
} else {
	$menu = TRUE;
}
$title = '';
$which_css = array(
"<link href='css/geral.css' rel='stylesheet' type='text/css' >",
"<link rel='stylesheet' type='text/css' href='css/cssmenu.css' >"
);
$which_java = array(
"<script type='text/javascript' src='css/cssmenuCore.js'></script>",
"<script type='text/javascript' src='css/cssmenuAddOns.js'></script>",
"<script type='text/javascript' src='css/cssmenuAddOnsItemBullet.js'></script>"
);
$body='';
$title = 'Busca plantas';
FazHeader($title,$body,$which_css,$which_java,$menu);

echo "<br />
<table class='myformtable' align='center' cellpadding=\"5\">
<thead>
<tr >
<td colspan='100%'>
".GetLangVar('namebuscar')." ".GetLangVar('nametaggedplant')."s&nbsp;&nbsp;<img height='15' src=\"icons/icon_question.gif\" ";
	$help = "Faz uma busca por arvores por uma lista com números das placas e gera um filtro. A lista deve ser separada por ponto e virgula";
	echo " onclick=\"javascript:alert('".$help."');\">
</td>
</tr>
</thead><tbody>
<form method='post' name='finalform' action='search-plantas-save.php'>
<input type='hidden' name='ispopup' value='$ispopup' >
";
if ($bgi % 2 == 0) {$bgcolor = $linecolor2 ;} else { $bgcolor = $linecolor1 ;} $bgi++;
echo "
<tr bgcolor = '".$bgcolor."'>
	<td class='tdsmallbold'>".GetLangVar('nameselect')." ".GetLangVar('namefiltro')."&nbsp;<img height='15' src=\"icons/icon_question.gif\" ";
	$help = "Precisa indicar um filtro que agrupa as árvores por o mesmo número de placas pode ter em locais diferentes";
	echo " onclick=\"javascript:alert('$help');\" /></td>
	<td><select name='filtro'>";
		if (!empty($filtro)) {
			$qq = "SELECT * FROM Filtros WHERE FiltroID='".$filtroid."'";
			$res = @mysql_query($qq,$conn);
			$rr = @mysql_fetch_assoc($res);
			echo "<option selected value='".$rr['FiltroID']."'>".$rr['FiltroName']."</option>";
		}
			echo "<option value=''>".GetLangVar('nameselect')."</option>";
			$qq = "SELECT * FROM Filtros WHERE AddedBy=".$_SESSION['userid']." OR Shared=1 ORDER BY FiltroName";
			$res = @mysql_query($qq,$conn);
			while ($rr = @mysql_fetch_assoc($res)) {
				echo "<option value='".$rr['FiltroID']."'>".$rr['FiltroName']."</option>";
			}

	echo "</select></td>
	</tr>
	";

if ($bgi % 2 == 0) {$bgcolor = $linecolor2 ;} else { $bgcolor = $linecolor1 ;} $bgi++;
echo "
<tr bgcolor = '".$bgcolor."'>
	<td class='tdsmallbold'>Lista de números de placas</td><td>
	<textarea name='tagnumbers' rows=8 cols=60>$plantaslist</textarea></td>
	</tr>
	";
echo "
<tr><td colspan='100%'>
	<table align='center'>
		<tr><td>
			<input type='submit' value='".GetLangVar('nameenviar')."' class='bsubmit' />
			</td>
			</form>
			<form method='post' action='search-plantas.php'>
			<input type='hidden' name='ispopup' value='$ispopup' >
			<td><input type='submit' value='".GetLangVar('namereset')."' class='breset' /></td>
			</form>
		</tr>
	</table>
</td></tr>
</tbody></table>";


$which_java = array("<script type='text/javascript' src='javascript/myjavascripts.js'></script>",
"<!-- Create Menu Settings: (Menu ID, Is Vertical, Show Timer, Hide Timer, On Click ('all' or 'lev2'), Right to Left, Horizontal Subs, Flush Left, Flush Top) -->",
"<script type='text/javascript'>qm_create(0,false,0,500,false,false,false,false,false);</script>");
FazFooter($which_java,$calendar=FALSE,$footer=$menu);

?>