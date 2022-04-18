<script language="JavaScript" src="javascripts/calendar_db.js"></script>
<link rel="stylesheet" href="stylesheets/calendar.css">

<?php

	//Script laden zodat je nooit pagina buiten de index om kan laden
	include("app/includes/resources/security.php");
	
	if ($gebruiker['admin'] < 2) { header("Location: ./home"); exit; }
	
	#################################################################
	
	if (isset($_POST['ban'])) {
		if (empty($_POST['player']))
			echo '<div class="red"> Digite o email da conta a ser banida.</div>';
		else if (empty($_POST['reden']))
			echo '<div class="red"> Digite uma razão.</div>';
		else{
		
		if ($_POST['tot'] == "") {
		$_POST['tot'] = "0000-00-00";
		}
		
			$select = DB::exQuery("SELECT acc_id FROM rekeningen WHERE email = '".$_POST['player']."'")->fetch_assoc();	
			DB::exQuery("UPDATE gebruikers SET banned='Y' WHERE acc_id = '".$select['acc_id']."'");
			DB::exQuery("UPDATE rekeningen SET account_code = '0', bloqueado='sim', bloqueado_tempo='".$_POST['tot']."', razaobloqueado='".$_POST['reden']."' WHERE email = '".$_POST['player']."'");
			
			echo '<div class="green"> A conta foi bloqueada com sucesso até '.$_POST['tot'].'.</div>';
		}
	}
	if (isset($_POST['take'])) {
	
		$select = DB::exQuery("SELECT acc_id FROM rekeningen WHERE email = '".$_POST['player']."'")->fetch_assoc();
		DB::exQuery("UPDATE gebruikers SET banned='N' WHERE acc_id = '".$select['acc_id']."'");
		DB::exQuery("UPDATE rekeningen SET account_code = '1', bloqueado='nao', bloqueado_tempo='0000-00-00', razaobloqueado='' WHERE email = '".$_POST['who']."'");
		echo '<div class="green"> A conta foi desbloqueada.</div>';
	}

?>
<form method="post" name="ban">
<center>
	<table width="400px" style="border: 1px solid #000;">
    	<tr>
        	<td colspan="2" height="40"><center><strong>Bloquear Contas</strong></center></td>
        </tr>
  
        <tr>
        	<td>E-mail da conta:</td>
            <td><input type="text" name="player" class="text_long" value="<?php if ($_POST['player'] != '') echo $_POST['player']; else echo $_GET['player']; ?>" /></td>
        </tr>
        <tr>
        	<td>Tempo:</td>
            <td><input type="text" name="tot" class="text_long" value="<?php if ($_POST['tot'] != '') echo $_POST['tot']; else echo "AAAA-MM-DD";?>" maxlength="10"/><br/>(Deixar tempo em branco para permanente)
		</td>
        </tr>
        <tr>
        	<td>Razão:</td>
            <td><input type="text" name="reden" class="text_long" value="<?php if ($_POST['reden'] != '') echo $_POST['reden']; ?>" maxlength="30" /></td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><input type="submit" name="ban" class="button" value="Bloquear" /></td>
        </tr>
    </table>
</center>
</form>

<div style="padding-top:30px;"></div>

<center>
	<table width="650px" style="border: 1px solid #000;">
    	<tr>
        	<td colspan="4" height="40"><center><strong>Lista de contas bloqueadas</strong></center></td>
        </tr>
        <tr>
        	<td width="120"><strong>Conta:</strong></td>
            <td width="110"><strong>Bloqueado até:</strong></td>
            <td width="200"><strong>Razão:</strong></td>
            <td width="50"><strong>Remover:</strong></td>
        </tr>
        <?php
        $query = DB::exQuery("SELECT * FROM rekeningen WHERE bloqueado='sim' ORDER BY bloqueado_tempo DESC");
		  for($j=$page+1; $ban = $query->fetch_assoc(); $j++)
		  { 
		  if ($ban['bloqueado_tempo'] == "0000-00-00") {
		  $tempoo = "Permanente";
		  }else {
		  $tempoo = implode("/",array_reverse(explode("-",$ban['bloqueado_tempo'])));
		  }
			  echo '
			  		<tr>
						<td>'.$ban['username'].' ('.$ban['email'].')</td>
			
						<td>'.$tempoo.'</td>
						<td>'.$ban['razaobloqueado'].'</td>
						<td><form method="post"><input type="hidden" name="who" value="'.$ban['email'].'" /><input type="submit" name="take" class="button_mini" value="OK"></form></td>
					</tr>';
		  }
		?>
    </table>
</center>      	