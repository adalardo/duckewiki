<?php
//Start session
ini_set("memory_limit","-1");
ini_set("mysql.allow_persistent","-1");
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
$ispopup=1;
if ($ispopup==1) {
	$menu = FALSE;
} else {
	$menu = TRUE;
}

//VARIAVEIS QUE VEM DE OUTROS LINKS

$tbname = 'processo_'.$processoid;

$vars = array('tbname'  =>  $tbname,
'processoid'  =>  $processoid,
'update' => $update+0
);

function emptyvar($var)
{
    // returns true whether the var is not empty
    return(!empty($var));
}
$vars = array_filter($vars,"emptyvar");
$varstxt = '';
$ii=0;
foreach ($vars as $kk => $vv) {
   if ($ii==0) {
		$varstxt .= $kk."=".$vv;
	} else {
		$varstxt .= "&".$kk."=".$vv;
	}
	$ii++;
}

//PREPARA ARQUIVO PARA LOOP DE PROGRESSO
$qqz = "DROP TABLE `temp_".$tbname."`";
@mysql_query($qqz,$conn);
$qqz = "CREATE TABLE `temp_".$tbname."`  (`percentage` INT(10) NOT NULL) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";
mysql_query($qqz,$conn);
$qqz = "INSERT INTO `temp_".$tbname."` (`percentage`) VALUES ('0');";
mysql_query($qqz,$conn);

//PREPARA O CABECALHO, CSS E JAVASCRIPTS
$which_css = array(
"<link href='css/geral.css' rel='stylesheet' type='text/css' >",
"<link rel='stylesheet' type='text/css' href='css/jquery-ui.css' />"
);
$which_java = array(
"<script src=\"javascript/jquery-1.10.2.js\"></script>",
"<script src=\"javascript/jquery-ui.js\"></script>",
"<script>
    function CheckProgress() {
      var time = new Date().getTime();
      $.get('processo-amostras-gridprogress.php', { t: time, tbname: '".$tbname."'}, function (data) {
          var progress = parseInt(data, 10);
          document.getElementById('probar').value = progress;
        if (progress < 100) {
          document.getElementById('probarperc').innerHTML = 'Processando ' + progress + '%';
          setTimeout(function() { CheckProgress();}, 1);
    	} else {
          document.getElementById('probarperc').innerHTML = progress + '%' +' CONCLUIDO';
          document.getElementById('loaderimg').style.visibility= 'hidden';
          window.location = 'processo-amostras-gridsave.php?processoid=".$processoid."&tbname=".$tbname."&ispopup=1';
    	}
      });
	}
    //start your long-running process 
    $.ajax(
            {
                type: 'GET',
                url: 'processo-amostras-gridscript.php',
                data: '".$varstxt."',
                async: true,
                success:
                    function (data) {
                        document.getElementById('probarperc').innerHTML = data ;
                        document.getElementById('loaderimg').style.visibility= 'hidden';
                        document.getElementById('coffeeid').style.visibility= 'hidden';
                        if (data=='Concluido') {
                           window.location = 'processo-amostras-gridsave.php?processoid=".$processoid."&tbname=".$tbname."&ispopup=1';
                        }
                    }
            });
   CheckProgress();         
</script>",
"<style>
#probar {
  width: 300px;
  height: 3em;
}
#probarperc {
  text-align: center;
  color: #000000;
}
#procont {
  width: 320px;
  margin: auto;
  text-align: center;
}
</style>"
);
$body='';
$title = 'Preparando uma tabela de edição das amostras!';
//FAZ O CABECALHO
FazHeader($title,$body,$which_css,$which_java,$menu);
//CORPO DO DOCUMENTO
echo "
<br />
<div id='procont'>
<progress id='probar' value='0' max='100'></progress>
<img id='loaderimg' src='icons/loadingcircle.gif'  height='30' />
<div id='probarperc'>Aguarde!</div>
<img id='coffeeid' src='icons/animatedcoffee.gif'  height='50' />
</div>
";
//DEFINE JAVASCRIPTS PARA O PE (FOOTER)
$which_java = array(
//"<script type='text/javascript' src='javascript/myjavascripts.js'></script>",
//"<!-- Create Menu Settings: (Menu ID, Is Vertical, Show Timer, Hide Timer, On Click ('all' or 'lev2'), Right to Left, Horizontal Subs, Flush Left, Flush Top) -->",
//"<script type='text/javascript'>qm_create(0,false,0,500,false,false,false,false,false);</script>"
);
//FAZ O PE DA PAGINA
FazFooter($which_java,$calendar=FALSE,$footer=$menu);
//}
?>