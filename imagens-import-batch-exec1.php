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

//POST
$ppost = $_SESSION['imgpost'];
@extract($ppost);

//CABECALHO
$qqn = "DROP TABLE `temp_imgprogress".$uuid ."`";
@mysql_query($qqn,$conn);
$qqz = "CREATE TABLE `temp_imgprogress".$uuid ."`  (`percentage` INT(10) NOT NULL) ENGINE = InnoDB";
@mysql_query($qqz,$conn);
$qqz = "INSERT INTO `temp_imgprogress".$uuid ."` (`percentage`) VALUES ('0');";
@mysql_query($qqz,$conn);

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
      $.get('imagens-import-batch-progress.php', { t: time}, function (data) {
          var progress = parseInt(data, 10);
          document.getElementById('probar').value = progress;
        if (progress < 95) {
          document.getElementById('probarperc').innerHTML = 'Processando ' + progress + '%';
          setTimeout(function() { CheckProgress();}, 1);
        } 
      });
    }
    $.ajax(
            {
                type: 'GET',
                url: 'imagens-import-batch-script1.php',
                async: true,
                success:
                    function (data) {
                            document.getElementById('probarperc').innerHTML = data;
                            document.getElementById('loaderimg').style.visibility= 'hidden';
                            document.getElementById('probar').style.visibility= 'hidden';
                            document.getElementById('coffeeid').style.visibility= 'hidden';
                            //window.location = 'imagens-import-batch-form2.php';          
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
  font-size: 1.5em;
  color: #000000;
}
#procont {
  margin: auto;
  text-align: center;
}
</style>"
);
$body='';
$title = 'Checando o nome dos arquivos!';
FazHeader($title,$body,$which_css,$which_java,$menu);
/*DEFINE PASTA TEMPORARIA ONDE SAO ARMAZENADOS OS ARQUIVOS DURANTE A IMPORTACAO (HA REFERENCIA A ESTA PASTA EM imagesupload-doit.php)*/
$tbn = "uploadDir_". $_SESSION['userid'];
$dir = "uploads/batch_images/".$tbn;

echo "
<br />
<div id='procont'>
<progress id='probar' value='0' max='100'></progress>
<img id='loaderimg' src='icons/loadingcircle.gif'  height='30' />
<div id='probarperc'>Aguarde!</div>
<img id='coffeeid' src='icons/animatedcoffee.gif'  height='50' />
</div>";

$which_java = array(
"<script type='text/javascript' src='javascript/myjavascripts.js'></script>"
);
FazFooter($which_java,$calendar=FALSE,$footer=$menu);
?>