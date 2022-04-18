<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");
	
#ALs er al een speler naam binnenkomt met een GET, deze laden
if (isset($_GET['player'])) $spelernaam = $_GET['player'];
else $spelernaam = $_POST['gebruiker'];

#Als er silver of gold naar een ander gestuurd word
if (isset($_POST['naargebruiker'])) {
  #Eventuele komma vervangen door punt
  if ($_POST['what'] == 'silver') $what = 'silver';
  else $what = 'gold';
  $bedrag = floor($_POST['send_amount']);
  
  #Is er wel een ontvanger?
  if (empty($_POST['gebruiker']))
    $bericht_send = '<div class="red">'.$txt['alert_no_receiver'].'</div>';  
  #Niets ingevoerd?
  else if ($bedrag == 0)
  	$bericht_send = '<div class="red">'.$txt['alert_nothing_insert'].'</div>'; 
  #Niets ingevoerd?
  else if (($what != 'silver') && ($what != 'gold'))
  	$bericht_send = '<div class="red">'.$txt['alert_no_silver_or_gold'].'</div>'; 
  #Sem ranking suficiente
  else if (($gebruiker['rank'] < 8) && ($what == 'gold'))
  	$bericht_send = '<div class="red">Você não tem rank suficiente.</div>'; 
  #Tentando enviar pra si proprio	
  else if (strtolower($_POST['gebruiker']) == strtolower($gebruiker['username']))
    $bericht_send = '<div class="red">'.$txt['alert_send_to_yourself'].'</div>';  
  #Bestaat de ontvanger wel?
  else if (DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `username`='".$_POST['gebruiker']."'")->num_rows == 0)
    $bericht_send = '<div class="red">'.$txt['alert_receiver_dont_exist'].'</div>';
  #is er wel een bedrag ingevoerd?
  else if (preg_match('/[A-Za-z_]+$/',$bedrag))
    $bericht = '<div class="red">'.$txt['alert_amount_unknown'].'</div>';
  #Kijken als het ingevoerde bedrag wel meer dan 0 is
  else if ($bedrag < 0)
    $bericht_send = '<div class="red">'.$txt['alert_amount_unknown'].'</div>';
  #Is er wel een silverig bedrag ingevoerd?
  else if (!is_numeric($bedrag))
    $bericht_send = '<div class="red">'.$txt['alert_amount_unknown'].'</div>';  
  #is het bedrag wel 10 of groter?
  else if (($what == 'silver') && (($bedrag != 0) && ($bedrag < 10)))
    $bericht_send = '<div class="red">'.$txt['alert_more_than_10silver'].'</div>';
  #Heeft de speler wel zo veel silver contant
  else if (($what == 'silver') && ($gebruiker['silver'] < $bedrag))
    $bericht_send = '<div class="red">'.$txt['alert_too_less_money'].'</div>';
  #Heeft de speler wel zo veel gold contant
  else if (($what == 'gold') && ($rekening['gold'] < $bedrag))
    $bericht_send = '<div class="red">'.$txt['alert_too_less_gold'].'</div>';

  else{
	#Message
    $bericht_send = '<div class="green">'.$txt['success_send'].'</div>';
    #silver bij jezelf verminderen
	if ($what == 'silver') {
		DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$bedrag."' WHERE `user_id`='".$_SESSION['id']."'");
		#5% van het bedrag afhalen
		$bedrag = $bedrag;
		#silver bij de tegen party ophogen
		DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$bedrag."' WHERE `username`='".$_POST['gebruiker']."'");
		$sg = 'silver';
	}
	else{
		DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-{$bedrag} WHERE `acc_id`={$_SESSION['acc_id']} LIMIT 1");
		
		
		$quemvai = DB::exQuery("select `acc_id` from `gebruikers` where `username`='".$_POST['gebruiker']."' limit 1")->fetch_assoc();
		DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+{$bedrag} WHERE `acc_id`={$quemvai['acc_id']} LIMIT 1");
		
		
		$sg = 'gold';
	}
		###Event
		$select = DB::exQuery("SELECT user_id FROM gebruikers WHERE username = '".$_POST['gebruiker']."'")->fetch_assoc();
		
		#Taal pack includen
		$eventlanguage = GetEventLanguage();
		include('language/events/language-events-'.$eventlanguage.'.php');
				
		#Bericht opstellen na wat de language van de user is
		$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a> '.$txt['event_gave_you'].' <img src="'.$static_url.'/images/icons/'.$sg.'.png" title="'.$sg.'" width="16" height="16" class="imglower"> '.highamount($bedrag).' '.$sg.'.';
				
		#Melding geven aan de uitdager
		DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen)
		VALUES (NULL, NOW(), '".$select['user_id']."', '".$event."', '0')");
		$date = date("Y-m-d H:i:s");
		DB::exQuery("INSERT INTO bank_logs (id, date, sender, reciever, amount, what)
		VALUES (NULL, NOW(), '".$gebruiker['username']."', '".$_POST['gebruiker']."', '".$bedrag."', '".$what."')");
  }
}

echo addNPCBox(5, 'Banco Pokémon', 'Aqui você pode fazer transferências de <b>Silvers</b> ou <b>Golds</b> para outros <b>Treinadores</b>! <br>No mínimo <img src="'.$static_url.'/images/icons/silver.png" title="Silver" /> 10 e <b>RANK MÍNIMO</b> para realizar transferências de Gold é <b>8 - New Duelist</b>!');
?>

<?php if ($bericht_send) echo $bericht_send; ?>

<div class="box-content" style="margin-bottom: 7px"><h3 class="title" style="background: none"> Silvers no Inventário: <img src="<?=$static_url?>/images/icons/silver.png" title="Silver" />  <?= $silver; ?></h3> </div>
<div class="box-content">
  <h3 class="title">TRANSFERÊNCIAS</h3>
  <form method="post" onsubmit="return confirm('Deseja realmente realizar esta transferência?');">
    <table width="37%" border="0" style="margin: 10px; text-align: center; padding: 10px">
      <tr>
        <td><b style="color: #9eadcd; font-size: 12px">Treinador:</b><br><input type="text" name="gebruiker" value="<?php if ($_GET['player'] != '') echo $_GET['player']; else echo $spelernaam; ?>" id="player" class="input-blue" required style="margin-top: 5px"/></td>
        <td><b style="color: #9eadcd; font-size: 12px">Valor:</b><br><input type="number" name="send_amount" value="<?php if (isset($_POST['send_amount'])) echo $_POST['send_amount']; ?>" id="send_amount" class="input-blue" min="10" required style="margin-top: 5px"/></td>
      </tr>
      <tr>
        <td style="text-align: right; padding: 4px 30px;"><input type="radio" name="what" value="silver" id="silver"  <?php if ($_POST['what'] != 'gold') echo 'checked'; ?> /> <label for="silver"><img src="<?=$static_url?>/images/icons/silver.png" alt="Silver" title="Silver" width="16" height="16" style="vertical-align: unset"/></label></td>
        <td style="text-align: left; padding: 4px 30px;"><label for="gold"><img src="<?=$static_url?>/images/icons/gold.png" id="gold" alt="Gold" title="Gold" width="16" height="16" style="vertical-align: unset; margin-right: 3px"/></label> <input type="radio" name="what" id="gold" value="gold"  <?php if ($_POST['what'] == 'gold') echo 'checked'; ?> />
      </tr>
    </table>
    <div style="border-top: 1px solid #577599;"><input type="submit" name="naargebruiker" value="Transferir" class="button" style="margin: 6px"/></div>
  </form>
</div>
<?php 
if (!empty($gebruiker['clan'])) {
    $infos = $clan->get($gebruiker['clan']);
?>
<div class="box-content" style="display: inline-block;margin-top: 7px;margin-bottom: 7px; width: 50%"><h3 class="title" style="background: none"> Silvers do Clã: <img src="<?=$static_url?>/images/icons/silver.png" title="Silver" />  <?= highamount($infos['silvers']); ?></h3></div>
<div class="box-content" style="display: inline-block;width: 49%;"><h3 class="title" style="background: none"> Golds do Clã: <img src="<?=$static_url?>/images/icons/gold.png" title="Golds" />  <?= highamount($infos['golds']); ?></h3></div>
<div class="box-content">
  <h3 class="title">TRANSFERÊNCIA PARA O CLÃ</h3>
  <form method="post" onsubmit="return confirm('Deseja realmente realizar esta transferência para seu Clã?');">
    <table width="37%" border="0" style="margin: 10px; text-align: center; padding: 10px">
      <tr>
        <td colspan="2"><b style="color: #9eadcd; font-size: 12px">Valor:</b><br><input type="number" name="send_amount_clan" value="<?php if (isset($_POST['send_amount'])) echo $_POST['send_amount']; ?>" id="send_amount_clan" class="input-blue" min="10" required style="margin-top: 5px"/></td>
      </tr>
      <tr>
        <td style="text-align: right; padding: 4px 30px;"><input type="radio" name="what_clan" value="silver" id="silver_clan"  <?php if ($_POST['what'] != 'gold') echo 'checked'; ?> /> <label for="silver_clan"><img src="<?=$static_url?>/images/icons/silver.png" alt="Silver" title="Silver" width="16" height="16" style="vertical-align: unset"/></label></td>
        <td style="text-align: left; padding: 4px 30px;"><label for="gold_clan"><img src="<?=$static_url?>/images/icons/gold.png" id="gold" alt="Gold" title="Gold" width="16" height="16" style="vertical-align: unset; margin-right: 3px"/></label> <input type="radio" name="what_clan" id="gold_clan" value="gold"  <?php if ($_POST['what'] == 'gold') echo 'checked'; ?> />
      </tr>
    </table>
    <div style="border-top: 1px solid #577599;"><input type="submit" name="naargebruiker_clan" value="Transferir" class="button" style="margin: 6px"/></div>
  </form>
</div>
<?php
}
?>