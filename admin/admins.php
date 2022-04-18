<?php

	//Script laden zodat je nooit pagina buiten de index om kan laden
	include("app/includes/resources/security.php");
	
//Admin controle
if ($gebruiker['admin'] < 3) {
  header('location: ./home');
  exit;
}

	##Geef iemand een starter
	#################################################################
	
	if (isset($_POST['make'])) {
		
		if (empty($_POST['make-admin'])) {
			echo '<div class="red"> Digite o nome de algum treinador.</div>';
		}
		else if (DB::exQuery("SELECT user_id FROM gebruikers WHERE username='".$_POST['make-admin']."'")->num_rows == 0) {
			echo '<div class="red"> '.$_POST['make-admin'].' não existe.</div>';
		}
		else if (DB::exQuery("SELECT user_id FROM gebruikers WHERE username='".$_POST['make-admin']."' and admin>='1'")->num_rows >= 1) {
			echo '<div class="blue"> '.$_POST['make-admin'].' já é membro da equipe.</div>';
		}
			
		else{
			DB::exQuery("UPDATE gebruikers SET admin = '1' WHERE username = '".$_POST['make-admin']."'");
			
			echo '<div class="green"> '.$_POST['make-admin'].' agora faz parte da equipe.</div>';
		}
	}
	
	if (isset($_POST['take'])) {
		
		DB::exQuery("UPDATE gebruikers SET admin = '0' WHERE username = '".$_POST['who']."'");
		
		echo '<div class="green">Os poderes de administrador de '.$_POST['who'].' foram removidos.</div>';
	}

?>
<form method="post">
<center>
	<table width="240" style="border: 1px solid #000;">
    	<tr>
        	<td colspan="2" height="40"><center><strong>Faça alguém moderador</strong></center></td>
        </tr>
        <tr>
        	<td width="80"><strong>Treinador:</strong></td>
            <td width="160"><input type="text" name="make-admin" class="text_long" value="<?php echo $_GET['player']; ?>" /></td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><input type="submit" name="make" class="button" value="Fazer mod"/></td>
        </tr>
    </table>
</center>
</form>
<div style="padding-top:30px;"></div>

<center>
	<table width="240" style="border: 1px solid #000;">
    	<tr>
        	<td colspan="2" height="40"><center><strong>Administrados level 1 (Moderador)</strong></center></td>
        </tr>
        <?php
        $query = DB::exQuery("SELECT username FROM gebruikers WHERE admin = '1'");
		  for($j=$page+1; $admin = $query->fetch_assoc(); $j++)
		  { 
			  echo '<form method="post"><input type="hidden" name="who" value="'.$admin['username'].'" />
			  		<tr>
						<td width="120"><div style="padding-left:20px;"><img src="'.$static_url.'/images/icons/user_admin.png" width="16" height="16" /> '.$admin['username'].'</div></td>
						<td width="120"><input type="submit" name="take" value="Remover" class="button_mini"></td>
					</tr></form>';
		  }
		?>
		<tr>
        	<td colspan="2" height="40"><center><strong>Administrados level 2 (Administrador)</strong></center></td>
        </tr>
        <?php
        $query = DB::exQuery("SELECT username FROM gebruikers WHERE admin = '2'");
		  for($j=$page+1; $admin = $query->fetch_assoc(); $j++)
		  { 
			  echo '<form method="post"><input type="hidden" name="who" value="'.$admin['username'].'" />
			  		<tr>
						<td width="120"><div style="padding-left:20px;"><img src="'.$static_url.'/images/icons/user_admin.png" width="16" height="16" /> '.$admin['username'].'</div></td>
						<td width="120"><input type="submit" name="take" value="Remover" class="button_mini"></td>
					</tr></form>';
		  }
		?>
		<tr>
        	<td colspan="2" height="40"><center><strong>Administrados level 3 (Dono)</strong></center></td>
        </tr>
        <?php
        $query = DB::exQuery("SELECT username FROM gebruikers WHERE admin = '3'");
		  for($j=$page+1; $admin = $query->fetch_assoc(); $j++)
		  { 
			  echo '<form method="post"><input type="hidden" name="who" value="'.$admin['username'].'" />
			  		<tr>
						<td width="120"><div style="padding-left:20px;"><img src="'.$static_url.'/images/icons/user_admin.png" width="16" height="16" /> '.$admin['username'].'</div></td>
						<td width="120"><input type="submit" name="take" value="Remover" class="button_mini"></td>
					</tr></form>';
		  }
		?>
    </table>
</center>
</form>