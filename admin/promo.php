<?php
header('Content-Type: text/html; charset=utf-8'); 
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 3) {
 header('location: ./home');
 exit;
 }

#####################################################################
	

$userfield = $_GET['player'];
if (isset($_POST['give'])) {
	
	if (empty($_POST['username'])) {
		echo '<div class="red"> Digite o nome de um treinador.</div>';
	}
	else if (DB::exQuery("SELECT user_id FROM gebruikers WHERE username = '".$_POST['username']."'")->num_rows == 0) {
		echo '<div class="red"> O treinador não existe.</div>';
	}
	else{
		$userfield = $_POST['username'];
		$moeda = $_POST['moeda'];
		
	  
	 
	 $pegauserid = DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `username`='".$_POST['username']."'")->fetch_assoc();
  
  
  		$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> Você recebeu '.$moeda.' moedas promocionais.';
		
		DB::exQuery("INSERT INTO `log_moeda_promos`(`user_id`, `user`, `qnt`) VALUES ('".$pegauserid['user_id']."', '".$_POST['username']."', '".$moeda."') ");
		
		DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen) VALUES (NULL, NOW(), '".$pegauserid['user_id']."', '".$event."', '0')");


	DB::exQuery("UPDATE `gebruikers` SET `moedapromocional`=`moedapromocional`+'".$moeda."' WHERE user_id='".$pegauserid['user_id']."'");

  
	  
	  
	  
		  echo '<div class="green"> '.$moeda.' moedas promocionais foram entregues para '.$_POST['username'].' com sucesso.</div>';
		  
		


	}
}
?>

<center>
<form method="post">
<table width="300">
	<tr>
    	<td>Treinador:</td>
        <td><input type="text" name="username" class="text_long" value="<?php echo $userfield; ?>" /></td>
    </tr>
    <tr>
    	<td>Moeda Promocional:</td>
        <td><input type="text" name="moeda" class="text_long" value="<?php echo $moeda; ?>" /></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
        <td><input type="submit" name="give" value="Entregar moedas!" class="button" /></td>
    </tr>
</table>
</form>
</center>