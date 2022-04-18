<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }
?>

<center>
<form method="post">
    <table width="300" border="0">
        <tr>
          <td><strong>IP:</strong></td>
          <td><input name="ip" type="text" value="<?php if ($_POST['ip'] != '') echo $_GET['ip']; else echo $_POST['ip']; ?>" class="text_long" maxlength="15"></td>
          <td><input name="submit1" type="submit" value="Procurar!" class="button_mini"></td>
        </tr>
     </table>
</form>
</center>

<?php
//Als er een ip opgegeven is
if ($_GET['ip'] != "") {
  $_POST['ip'] = $_GET['ip'];
}

if (isset($_POST['ip'])) {
  //Is er wel een ip opgegeven zo ja dan verder
  if ($_POST['ip'] != "") {
    //Gegevens laden van het ingevoerde ip
	if ($_GET['which'] == 'aangemeld') {
  		$dbres = DB::exQuery("SELECT `acc_id`, `username`, `ip_aangemeld`, `ip_ingelogd`, `email` FROM `rekeningen` WHERE `account_code`='1' AND `ip_aangemeld`='".$_POST['ip']."' ORDER BY `username`");
	}
	else{
		$dbres = DB::exQuery("SELECT `acc_id`, `username`, `ip_aangemeld`, `ip_ingelogd`, `email` FROM `rekeningen` WHERE `account_code`='1' AND `ip_ingelogd`='".$_POST['ip']."' ORDER BY `username`");
	}
  	//Beeldweergave
  	echo '<center><br /><table width="500">
    		<tr>
    			<td width="50">#</td>
   				<td width="120"><strong>Conta:</strong></td>
    			<td width="120"><strong>IP Registro:</strong></td>
    			<td width="120"><strong>IP Login:</strong></td>
    			<td width="90"><strong>Banir:</strong></td>
    		</tr>';
  	
  	//Lijst opbouwen per speler gaat vanzelf
    for($j=$pagina+1; $gegevens = $dbres->fetch_assoc(); $j++)
    {
      echo '<tr>
      				<td height="30">'.$j.'.</td>
      				<td>'.$gegevens['username'].'</td>
      				<td>'.$gegevens['ip_aangemeld'].'</td>
      				<td>'.$gegevens['ip_ingelogd'].'</td>';
					if ($_GET['which'] == 'aangemeld') {
  						echo '<td><a href="./admin/ban-ip&ip='.$gegevens['ip_aangemeld'].'&player='.$gegevens['acc_id'].'"><img src="../images/icons/user_ban.png" alt="Banir" title="Banir IP Registro."></a></td>';
					}
					else{
						echo '<td><a href="./admin/ban-ip&ip='.$gegevens['ip_ingelogd'].'&player='.$gegevens['acc_id'].'"><img src="../images/icons/user_ban.png" alt="Banir" title="Banir IP Login."></a></td>';
					}
      			echo '</tr>';
    }
  }
}
?>
</table></center>