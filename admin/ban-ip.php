<script language="JavaScript" src="javascripts/calendar_db.js"></script>
<link rel="stylesheet" href="stylesheets/calendar.css">

<?php

	//Script laden zodat je nooit pagina buiten de index om kan laden
	include("app/includes/resources/security.php");
	
	if ($gebruiker['admin'] < 2) { header("Location: ./home"); exit; }
	
	#################################################################
	
	if (isset($_POST['ban'])) {
		if (empty($_POST['ip']))
			echo '<div class="red"> Digite um ip válido.</div>';
		else if (empty($_POST['tot']))
			echo '<div class="red"> Digite um tempo.</div>';
		else if (empty($_POST['reden']))
			echo '<div class="red"> Digite uma razão.</div>';
		else{
			DB::ExQuery("INSERT INTO ban (ip, user_id, tot, reden)
						VALUES ('".$_POST['ip']."', '".$_POST['player']."', '".$_POST['tot']."', '".$_POST['reden']."')");
			
			echo '<div class="green"> O IP '.$_POST['ip'].' foi banido com sucesso até '.$_POST['tot'].'.</div>';
		}
	}
	if (isset($_POST['take'])) {
		DB::ExQuery("DELETE FROM ban WHERE ip = '".$_POST['who']."'");
		echo '<div class="green"> O ip foi desbanido.</div>';
	}

?>
<form method="post" name="ban">
<center>
	<table width="300" style="border: 1px solid #000;">
    	<tr>
        	<td colspan="2" height="40"><center><strong>Banir IP</strong></center></td>
        </tr>
        <tr>
        	<td>IP:</td>
            <td><input type="text" name="ip" class="text_long" value="<?php if ($_POST['ip'] != '') echo $_POST['ip']; else echo $_GET['ip']; ?>" maxlength="15" /></td>
        </tr>


        <tr>
        	<td>Tempo:</td>
            <td><input type="text" name="tot" class="text_long" value="<?php if ($_POST['tot'] != '') echo $_POST['tot']; ?>" maxlength="10"/>
			<script language="JavaScript">
				new tcal ({
					// form name
					'formname': 'ban',
					// input name
					'controlname': 'tot'
				});
			</script></td>
        </tr>
        <tr>
        	<td>Razão:</td>
            <td><input type="text" name="reden" class="text_long" value="<?php if ($_POST['reden'] != '') echo $_POST['reden']; ?>" maxlength="30" /></td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
            <td><input type="submit" name="ban" class="button" value="Banir IP" /><br>Lembre-se que serve apenas para bloquear um IP especifico, sem banir contas/chars envolvidos.</td>
        </tr>
    </table>
</center>
</form>

<div style="padding-top:30px;"></div>

<center>
	<table width="600" style="border: 1px solid #000;">
    	<tr>
        	<td colspan="4" height="40"><center><strong>Lista de IPS banidos</strong></center></td>
        </tr>
        <tr>
            <td width="120"><strong>IP:</strong></td>
            <td width="110"><strong>Banido até:</strong></td>
            <td width="200"><strong>Razão:</strong></td>
            <td width="50"><strong>Remover:</strong></td>
        </tr>
        <?php
        $query = DB::ExQuery("SELECT * FROM ban ORDER BY tot DESC");
		  for($j=$page+1; $ban = $query->fetch_assoc(); $j++)
		  { 
			  echo '
			  		<tr>
						<td>'.$ban['ip'].'</td>
						<td>'.$ban['tot'].'</td>
						<td>'.$ban['reden'].'</td>
						<td><form method="post"><input type="hidden" name="who" value="'.$ban['ip'].'" /><input type="submit" name="take" class="button_mini" value="OK"></form></td>
					</tr>';
		  }
		?>
    </table>
</center>      	