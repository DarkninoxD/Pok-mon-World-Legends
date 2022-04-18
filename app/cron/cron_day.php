<?php
require_once('../includes/resources/config.php');
	
/*	SCRIPT QUE LIMPA AS CONTAS INATIVAS A MAIS DE 1 MÊS
	
	#Tijd uit de database halen, van de mensen die niet geactiveerd zjin
  	$sql = DB::exQuery("SELECT datum, user_id, username FROM `gebruikers` WHERE `account_code`!='1' AND `account_code`!='0'");
  	while($gegeven  = $sql->fetch_assoc()) {
		#Als het meer dan een week geleden is. Dan verwijderen
		$tijdtoen = strtotime($gegeven['datum']);
		$tijdnu   = strtotime(date('Y-m-d H:i:s'))-2592000;
		if ($tijdtoen < $tijdnu) {
		  DB::exQuery("DELETE FROM `gebruikers` WHERE `user_id`='".$gegeven['user_id']."'");
		  DB::exQuery("DELETE FROM `gebruikers_item` WHERE `user_id`='".$gegeven['user_id']."'");
		  DB::exQuery("DELETE FROM `gebruikers_badges` WHERE `user_id`='".$gegeven['user_id']."'");
		  DB::exQuery("DELETE FROM `pokemon_speler` WHERE `user_id`='".$gegeven['user_id']."'");
		  DB::exQuery("DELETE FROM `pokemon_gezien` WHERE `user_id`='".$gegeven['user_id']."'");
    }
  }
*/


  $i = 0;
  $profiles1=DB::exQuery("SELECT user_id,acc_id FROM `gebruikers` WHERE `banned` != 'Y' ORDER BY `fishing` DESC LIMIT 3");
  while($profiles=$profiles1->fetch_assoc()) {
    $i++;
    if ($i == 1) {
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'20000' WHERE `user_id`='".$profiles['user_id']."'");
      DB::exQuery("UPDATE `fishs` SET `fish`='".$profiles['user_id']."'  WHERE `id`='1'");
      #Bericht opstellen na wat de language van de user is
      $event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" />Você ficou em 1 lugar no torneio de pesca. Ganhou 20000<img src="'.$static_url.'/images/icons/silver.png">.';
            
      #Melding geven aan de uitdager
      DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen) VALUES (NULL, NOW(), '".$profiles['user_id']."', '".$event."', '0')");
    }
    if ($i == 2) {
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'10000' WHERE `user_id`='".$profiles['user_id']."'");
      DB::exQuery("UPDATE `fishs` SET `fish2`='".$profiles['user_id']."'  WHERE `id`='1'");
      #Bericht opstellen na wat de language van de user is
      $event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" />Você ficou em 2 lugar no torneio de pesca. Ganhou 10000<img src="'.$static_url.'/images/icons/silver.png">.';
            
      #Melding geven aan de uitdager
      DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen)   VALUES (NULL, NOW(), '".$profiles['user_id']."', '".$event."', '0')");
    }
    if ($i == 3) {
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'5000' WHERE `user_id`='".$profiles['user_id']."'");
      DB::exQuery("UPDATE `fishs` SET `fish3`='".$profiles['user_id']."'  WHERE `id`='1'");
      #Bericht opstellen na wat de language van de user is
      $event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" />Você ficou em 3 lugar no torneio de pesca. Ganhou 5000<img src="'.$static_url.'/images/icons/silver.png">.';
            
      #Melding geven aan de uitdager
      DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen)   VALUES (NULL, NOW(), '".$profiles['user_id']."', '".$event."', '0')");
    }
  }
  DB::exQuery("UPDATE `gebruikers` SET `fishing`='0'");


/*
  #Jarigen een mail sturen
  $datenow = date('m-d');
  $birthdaysql = DB::exQuery("SELECT `username`, `email`, `land` FROM `gebruikers` WHERE `geb_datum` LIKE '%".$datenow."' AND `account_code`='1'");
  while($birthday  = $birthdaysql->fetch_assoc()) {
  
	if (($birthday['land'] == 'Netherlands') || ($birthday['land'] == 'Belgium'))
		$birthday_message = 'Feliz aniversário '.$birthday['username'].'!<br /><br />
		Nós temos um presente para você. <strong><img src="http://www.simbolarena.com.br/images/icons/silver.png"> 2500!</strong><br />
		Entre agora e confira seu presente.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia!<br /><br />
		Equipe Pokémon World Legends.';
	else if ($birthday['land'] == 'Germany')
		$birthday_message = 'Feliz aniversário '.$birthday['username'].'!<br /><br />
		Nós temos um presente para você. <strong><img src="http://www.simbolarena.com.br/images/icons/silver.png"> 2500!</strong><br />
		Entre agora e confira seu presente.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia!<br /><br />
		Equipe Pokémon World Legends.';
	else if ($birthday['land'] == 'Spain')
	$birthday_message = 'Feliz aniversário '.$birthday['username'].'!<br /><br />
		Nós temos um presente para você. <strong><img src="http://www.simbolarena.com.br/images/icons/silver.png"> 2500!</strong><br />
		Entre agora e confira seu presente.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia!<br /><br />
		Equipe Pokémon World Legends.';
	else if ($birthday['land'] == 'Poland')
		$birthday_message = 'Feliz aniversário '.$birthday['username'].'!<br /><br />
		Nós temos um presente para você. <strong><img src="http://www.simbolarena.com.br/images/icons/silver.png"> 2500!</strong><br />
		Entre agora e confira seu presente.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia!<br /><br />
		Equipe Pokémon World Legends.';
	else
		$birthday_message = 'Feliz aniversário '.$birthday['username'].'!<br /><br />
		Nós temos um presente para você. <strong><img src="http://www.simbolarena.com.br/images/icons/silver.png"> 2500!</strong><br />
		Entre agora e confira seu presente.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia!<br /><br />
		Equipe Pokémon World Legends.';
	  
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
    $headers .= "From: Pokemon World Legends <automatico@simbolarena.com.br>\n"; 
    $headers .= "X-Sender: \"rot\" \n";  
    $headers .= "X-Mailer: PHP\n"; 
    $headers .= "Bcc: simbolarena.com.br\r\n"; 
	
    #Mail versturen
    mail($birthday['email'],
    'Feliz aniversário',
    '<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
    <center>
      <table width="80%" border="0" cellspacing="0" cellpadding="0">
        <tr>
      <td background="http://www.simbolarena.com.br/images/mail/linksboven.gif" width="11" height="11"></td>
      <td height="11" background="http://www.simbolarena.com.br/images/mail/bovenbalk.gif"></td>
      <td background="http://www.simbolarena.com.br/images/mail/rechtsboven.gif" width="11" height="11"></td>
        </tr>
    
        <tr>
      <td width="11" rowspan="2" background="http://www.simbolarena.com.br/images/mail/linksbalk.gif"></td>
      <td align="center" bgcolor="#D3E9F5"><img src="http://www.simbolarena.com.br/images/layout/logo.png" ></td>
      <td width="11" rowspan="2" background="http://www.simbolarena.com.br/images/mail/rechtsbalk.gif"></td>
        </tr>
        <tr>
          <td align="center" valign="top" bgcolor="#D3E9F5">'.$birthday_message.'</td>
        </tr>
        <tr>
      <td background="http://www.simbolarena.com.br/images/mail/linksonder.gif" width="11" height="11"></td>
      <td background="http://www.simbolarena.com.br/images/mail/onderbalk.gif" height="11"></td>
      <td background="http://www.simbolarena.com.br/images/mail/rechtsonder.gif" width="11" height="11"></td>
        </tr>
      </table><br>
      &copy; Pokémon World Legends
    </center>
    </body>
      </html>',$headers);
  }
*/
  /*
  #Pokemon missen je
  $time_1 = time()-600000;
  $time_2 = time()-700000;
  
  $misssql = DB::exQuery("SELECT `username`, `email`, `land` FROM `gebruikers` WHERE (`online` BETWEEN ".$time_1." AND ".$time_2.") AND `account_code`='1'");
  while($miss  = $misssql->fetch_assoc()) {
  
	if (($miss['land'] == 'Netherlands') || ($miss['land'] == 'Belgium'))
	$miss_message = 'Olá '.$miss['username'].'!<br /><br />
		Seus pokémons sentem sua falta.<br />
		Aproveite agora entre e treine eles.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia,<br /><br />
		Equipe Pokémon World Legends';
	else if ($miss['land'] == 'Germany')
		$miss_message = 'Olá '.$miss['username'].'!<br /><br />
		Seus pokémons sentem sua falta.<br />
		Aproveite agora entre e treine eles.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia,<br /><br />
		Equipe Pokémon World Legends';
	else if ($miss['land'] == 'Spain')
		$miss_message = 'Olá '.$miss['username'].'!<br /><br />
		Seus pokémons sentem sua falta.<br />
		Aproveite agora entre e treine eles.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia,<br /><br />
		Equipe Pokémon World Legends';
	else if ($miss['land'] == 'Poland')
	$miss_message = 'Olá '.$miss['username'].'!<br /><br />
		Seus pokémons sentem sua falta.<br />
		Aproveite agora entre e treine eles.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia,<br /><br />
		Equipe Pokémon World Legends';
	else
		$miss_message = 'Olá '.$miss['username'].'!<br /><br />
		Seus pokémons sentem sua falta.<br />
		Aproveite agora entre e treine eles.<br />
		<a href="http://www.simbolarena.com.br">www.simbolarena.com.br</a><br /><br />
		Tenha um bom dia,<br /><br />
		Equipe Pokémon World Legends';
	  
      $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
    $headers .= "From: Pokemon World Legends <automatico@simbolarena.com.br>\n"; 
    $headers .= "X-Sender: \"rot\" \n";  
    $headers .= "X-Mailer: PHP\n"; 
    $headers .= "Bcc: simbolarena.com.br\r\n";  
	
    #Mail versturen
    mail($miss['email'],
    'Important message',
    '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<center>
  <table width="80%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td background="images/mail/linksboven.gif" width="11" height="11"></td>
      <td height="11" background="images/mail/bovenbalk.gif"></td>
      <td background="images/mail/rechtsboven.gif" width="11" height="11"></td>
    </tr>
    <tr>
      <td width="11" rowspan="2" background="http://www.simbolarena.com.br/images/mail/linksbalk.gif"></td>
      <td align="center" bgcolor="#D3E9F5"><img src="http://www.simbolarena.com.br/images/mail/headermail.png" width="520" height="140"></td>
      <td width="11" rowspan="2" background="http://www.simbolarena.com.br/images/mail/rechtsbalk.gif"></td>
    </tr>
    <tr>
      <td valign="top" bgcolor="#D3E9F5">'.$miss_message.'</td>
    </tr>
    <tr>
      <td background="images/mail/linksonder.gif" width="11" height="11"></td>
      <td background="images/mail/onderbalk.gif" height="11"></td>
      <td background="images/mail/rechtsonder.gif" width="11" height="11"></td>
    </tr>
  </table>
  &copy; Pokémon World Legends '.date('Y').'
</center>
</body>
      </html>',$headers);
  }
*/
 
 
 #Desbloqueia os bloqueados
  $datablock = date('Y-m-d');
  DB::exQuery("UPDATE `gebruikers` SET banned = 'N', bloqueado='nao', bloqueado_tempo='0000-00-00', razaobloqueado='' WHERE `bloqueado_tempo`='".$datablock."'");
 
 DB::exQuery("UPDATE `gebruikers` SET `daily_bonus`='0'");
  
 DB::exQuery("DELETE FROM `friends` WHERE `date_to_remove`='$datablock' AND `accept`='0'");
  #Geef wat silver aan een jarige
  //DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'2500' WHERE `geb_datum`='".$datenow."' AND `account_code`='1'");
 
  #Wel premium setting
  DB::exQuery("UPDATE `gebruikers` SET `stelen`='3', `geluksrad`='3', `storten`='5', `puffins`='10', `calc_limit`='15', `calc_multiplier`='0' WHERE `premiumaccount`>UNIX_TIMESTAMP()");

  #Niet premium settings
  DB::exQuery("UPDATE `gebruikers` SET `stelen`='1', `geluksrad`='1', `storten`='3', `puffins`='5', `calc_limit`='5', `calc_multiplier`='0' WHERE `premiumaccount`<UNIX_TIMESTAMP()");

  #Area Dragon weer ff naar ander gebied + regio transporteren
  $gebiedrand = date('w');
  $wereldrand = rand(1,6);
  $gebiedrand2 = date('w');
  $wereldrand2 = rand(1,6);
  $gebiedrand3 = date('w');
  $wereldrand3 = rand(1,6);
  $gebiedrand4 = date('w');
  $wereldrand4 = rand(1,6);      
  if ($wereldrand == 1) $wereld = 'Kanto';
  else if ($wereldrand == 2) $wereld = 'Johto';
  else if ($wereldrand == 3) $wereld = 'Hoenn';
  else if ($wereldrand == 4) $wereld = 'Sinnoh';
  else if ($wereldrand == 5) $wereld = 'Unova';
  else if ($wereldrand == 6) $wereld = 'Kalos';
  
  if ($gebiedrand == 0) $gebied = 'Lavagrot';
  else if ($gebiedrand == 1) $gebied = 'Grot';
  else if ($gebiedrand == 2) $gebied = 'Gras';
  else if ($gebiedrand == 3) $gebied = 'Spookhuis';
  else if ($gebiedrand == 4) $gebied = 'Vechtschool';
  else if ($gebiedrand == 5) $gebied = 'Strand';
  else if ($gebiedrand == 6) $gebied = 'Water';
  
   if ($wereldrand2 == 1) $wereld2 = 'Kanto';
  else if ($wereldrand2 == 2) $wereld2 = 'Johto';
  else if ($wereldrand2 == 3) $wereld2 = 'Hoenn';
  else if ($wereldrand2 == 4) $wereld2 = 'Sinnoh';
  else if ($wereldrand2 == 5) $wereld2 = 'Unova';
  else if ($wereldrand2 == 6) $wereld2 = 'Kalos';
  
  if ($gebiedrand2 == 0) $gebied2 = 'Lavagrot';
  else if ($gebiedrand2 == 1) $gebied2 = 'Grot';
  else if ($gebiedrand2 == 2) $gebied2 = 'Gras';
  else if ($gebiedrand2 == 3) $gebied2 = 'Spookhuis';
  else if ($gebiedrand2 == 4) $gebied2 = 'Vechtschool';
  else if ($gebiedrand2 == 5) $gebied2 = 'Strand';
  else if ($gebiedrand2 == 6) $gebied2 = 'Water'; 
  
  if ($wereldrand3 == 1) $wereld3 = 'Kanto';
  else if ($wereldrand3 == 2) $wereld3 = 'Johto';
  else if ($wereldrand3 == 3) $wereld3 = 'Hoenn';
  else if ($wereldrand3 == 4) $wereld3 = 'Sinnoh';
  else if ($wereldrand3 == 5) $wereld3 = 'Unova';
  else if ($wereldrand3 == 6) $wereld3 = 'Kalos';
  
  if ($gebiedrand3 == 0) $gebied3 = 'Lavagrot';
  else if ($gebiedrand3 == 1) $gebied3 = 'Grot';
  else if ($gebiedrand3 == 2) $gebied3 = 'Gras';
  else if ($gebiedrand3 == 3) $gebied3 = 'Spookhuis';
  else if ($gebiedrand3 == 4) $gebied3 = 'Vechtschool';
  else if ($gebiedrand3 == 5) $gebied3 = 'Strand';
  else if ($gebiedrand3 == 6) $gebied3 = 'Water'; 
  
  if ($wereldrand4 == 1) $wereld4 = 'Kanto';
  else if ($wereldrand4 == 2) $wereld4 = 'Johto';
  else if ($wereldrand4 == 3) $wereld4 = 'Hoenn';
  else if ($wereldrand4 == 4) $wereld4 = 'Sinnoh';
  else if ($wereldrand4 == 5) $wereld4 = 'Unova';
  else if ($wereldrand4 == 6) $wereld4 = 'Kalos';
  
  if ($gebiedrand4 == 0) $gebied4 = 'Lavagrot';
  else if ($gebiedrand4 == 1) $gebied4 = 'Grot';
  else if ($gebiedrand4 == 2) $gebied4 = 'Gras';
  else if ($gebiedrand4 == 3) $gebied4 = 'Spookhuis';
  else if ($gebiedrand4 == 4) $gebied4 = 'Vechtschool';
  else if ($gebiedrand4 == 5) $gebied4 = 'Strand';
  else if ($gebiedrand4 == 6) $gebied4 = 'Water'; 
  
  
  //DB::exQuery("UPDATE pokemon_wild SET wereld = '".$wereld."', gebied = '".$gebied."' WHERE wild_id = '800'");
  DB::exQuery("UPDATE pokemon_wild SET wereld = '".$wereld2."', gebied = '".$gebied2."' WHERE wild_id = '895'");
  DB::exQuery("UPDATE pokemon_wild SET wereld = '".$wereld3."', gebied = '".$gebied3."' WHERE wild_id = '840'");
  DB::exQuery("UPDATE pokemon_wild SET wereld = '".$wereld4."', gebied = '".$gebied4."' WHERE wild_id = '923'");
  
  #2dias nas vendas diretas volta pra casa!
  $old_date = date("d/m/Y", time()-259200);
  $trans_old_sql = DB::exQuery("SELECT id, pokemon_id FROM transferlijst WHERE datum='".$old_date."' AND `type`='direct'");
  while($trans_old = $trans_old_sql->fetch_assoc()) {
    DB::exQuery("UPDATE `pokemon_speler` SET `opzak`='nee' WHERE `id`='".$trans_old['pokemon_id']."'");
  } 
 
  DB::exQuery("DELETE FROM `transferlijst` WHERE `datum`='".$old_date."' AND `type`='direct'");
  
  #Traders veranderen
  DB::exQuery("UPDATE traders SET wil = ''");
  
  #Markt eitjes voorraad wijzigen op Maandag en Donderdag
  if ((date('w') == 1) OR (date('w') == 5)) DB::exQuery("UPDATE markt SET beschikbaar = '0' WHERE soort = 'pokemon'");
  
  #Tijd opslaan van wanneer deze file is uitevoerd
  DB::exQuery("UPDATE `cron` SET `tijd`='".date("Y-m-d H:i:s")."' WHERE `soort`='dag'");
  
  if ($tripleexpfds) {
    if (date("w") == 0 OR date("w") == 6) DB::exQuery("UPDATE `configs` SET `valor`='3' WHERE `config`='exp'"); //DOMINGO && SABADO
    if (date("w") == 1) DB::exQuery("UPDATE `configs` SET `valor`='2' WHERE `config`='exp'"); //DESATIVA NA SEG
  }
  if ($doublesilverativo) {
    if (date("w") == $doublesilverdia) DB::exQuery("UPDATE `configs` SET `valor`='2' WHERE `config`='silver'"); //ATIVA
    if (date("w") != $doublesilverdia) DB::exQuery("UPDATE `configs` SET `valor`='1' WHERE `config`='silver'"); //DESATIVA 
  }

  #DELETE MENSAGENS HIDDEN PARA TODOS
  $msgs = DB::exQuery ("SELECT * FROM `conversas` WHERE trainer_1_hidden='1' AND trainer_2_hidden='1'");
  if ($msgs->num_rows > 0) {
    $msgs = $msgs->fetch_assoc();
    DB::exQuery ("DELETE FROM `conversas_messages` WHERE conversa='$msgs[id]'");
    DB::exQuery ("DELETE FROM `conversas` WHERE trainer_1_hidden='1' AND trainer_2_hidden='1'");
  }
  
  // //DROPS CHANCE
  // $chance = array('1', '3', '1', '2', '3', '2', '2');
  // //Fire, Fight, Grass, Ghost, Ground, Water, Ice
  // for($i = 0; $i < count($chance); $i++) {
  //     $id = $i+1;
  //     DB::exQuery("UPDATE `events_drop_1_2019` SET `chance` = '".$chance[$i]."' WHERE `id` = '".$id."'");
  // }
 
  
  #Optimiza tabelas
  DB::exQuery("OPTIMIZE TABLE `rekeningen`");
  DB::exQuery("OPTIMIZE TABLE `gebruikers`");
  DB::exQuery("OPTIMIZE TABLE `gebruikers_item`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_speler`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_speler_gevecht`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_wild`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_wild_gevecht`");
  DB::exQuery("OPTIMIZE TABLE `conversas`");
  DB::exQuery("OPTIMIZE TABLE `conversas_messages`");
  DB::exQuery("OPTIMIZE TABLE `aanval_log`");
  DB::exQuery("OPTIMIZE TABLE `cron`");
  DB::exQuery("OPTIMIZE TABLE `inlog_fout`");
  DB::exQuery("OPTIMIZE TABLE `inlog_logs`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_gezien`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_nieuw_baby`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_nieuw_starter`");
  DB::exQuery("OPTIMIZE TABLE `pokemon_nieuw_gewoon`");
  DB::exQuery("OPTIMIZE TABLE `bank_logs`");
  DB::exQuery("OPTIMIZE TABLE `battle_logs`");
  DB::exQuery("OPTIMIZE TABLE `release_log`");
  DB::exQuery("OPTIMIZE TABLE `transferlist_log`");   
      
  
  echo "Cron executado com sucesso.";
  
?>