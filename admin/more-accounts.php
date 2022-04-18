<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }

?>

<form method="post">
  <center><a href="#"><input type="submit" name="aanmeld" value="IP Cadastrado" class="button"></a> - <input type="submit" name="login" value="IP Logado" class="button"><br /><br /></center>
</form>
<?php
//Als er op 1 van de 2 word gedrukt
if ((isset($_POST['aanmeld'])) OR (isset($_POST['login']))) {

 //dubbel login
  if (isset($_POST['login'])) {
	
		$select = DB::exQuery("SELECT * FROM `inlog_logs` ORDER BY `datum` LIMIT 0,10");
	
	while ($sel = $select->fetch_assoc()) {
	
		$sele = DB::exQuery("SELECT `ip` FROM `inlog_logs` WHERE ip='".$sel['ip']."' ");
		
		$tel = $sele->num_rows;
		
		if ($tel > 1) {
		echo $sel['speler'] ." logou em mais de uma conta! <br />";
		}
	
	
	}
	
  }
 
 
 //dubbel aanmeld
  if (isset($_POST['aanmeld'])) {
	
	$select = DB::exQuery("SELECT * FROM `rekeningen` ORDER BY `acc_id` LIMIT 0,10");
	
	while ($sel = $select->fetch_assoc()) {
	
		$sele = DB::exQuery("SELECT `ip_aangemeld` FROM `rekeningen` WHERE ip_aangemeld='".$sel['ip_aangemeld']."' ");
		
		$tel = $sele->num_rows;
		
		if ($tel > 1) {
		echo $sel['username'] ." tem mais de uma conta no ip! <br />";
		}
	
	
	}
  
  }

 }

?>
</table>

