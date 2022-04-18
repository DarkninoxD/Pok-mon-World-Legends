<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1) {
  header('location: ./home');
  exit;
}

$homepage = DB::exQuery("SELECT text_en, text_de, text_es, text_nl, text_pl FROM bovenstuk")->fetch_assoc();

if (isset($_POST['change'])) {
	
	DB::exQuery("UPDATE bovenstuk SET text_en = '".$_POST['en']."', text_de = '".$_POST['de']."', text_es = '".$_POST['es']."', text_nl = '".$_POST['nl']."', text_pl = '".$_POST['pl']."'");
	
	echo '<div class="green">Atualizado com sucesso!</div>';
	
	$homepage['text_en'] = $_POST['en'];
	$homepage['text_de'] = $_POST['de'];
	$homepage['text_es'] = $_POST['es'];
	$homepage['text_nl'] = $_POST['nl'];
	$homepage['text_pl'] = $_POST['pl'];
}

?>

<form method="post">
<center>
<div style="padding: 6px 0px 6px 0px;"><strong>Pagina inicial<strong>
	<table width="600" cellpadding="0" cellspacing="0">
    	<tr>
            <td><textarea style="width:580px;" class="text_area" rows="15" name="en"><?php echo $homepage['text_en']; ?></textarea></td>
        </tr>
	      <td><div style="padding-top:10px;"><input type="submit" name="change" value="Atualizar!" class="button" /></div></td>
        </tr>
    </table> 
</div>     
</center>
</form>