<?php
header('Content-Type: text/html; charset=utf-8'); 
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 2) {
 header('location: ./home');
 exit;
 }


if (isset($_POST['give'])) {
	
	if (empty($_POST['silver'])) {
		echo '<div class="red"> Escolha um valor de silver.</div>';
	}
	else if ($_POST['silver'] != 1 AND $_POST['silver'] != 2 AND $_POST['silver'] != 3) {
		echo '<div class="red"> Escolha um valor de silver.</div>';
	}
	else{
		
  	          DB::exQuery("UPDATE `configs` SET `valor`='".$_POST['silver']."' WHERE `config`='silver'");
		  #Error tonen
		  echo '<div class="green"> Silver modificado com sucesso.</div>';


	}
}


 $ver1 = DB::exQuery("SELECT * FROM configs WHERE config='silver'")->fetch_assoc();

if ($ver1['valor'] == 1) {
$t1 = "checked";
}if ($ver1['valor'] == 2) {
$t2 = "checked";
}
if ($ver1['valor'] == 3) {
$t3 = "checked";
}
?>

<center>
<form method="post">
<table width="500">
	<tr>
    	<td>Silver:</td>
        <td>
<input type="radio" name="silver" value="1" <?php echo $t1; ?>> 1x (Normal)
<input type="radio" name="silver" value="2" <?php echo $t2; ?>> 2x (Double)
<input type="radio" name="silver" value="3" <?php echo $t3; ?>> 3x (Triple)




      </td>
    </tr>

    <tr>
    	<td>&nbsp;</td>
        <td><input type="submit" name="give" value="Alterar!" class="button" /></td>
    </tr>
</table>
</form>
</center>