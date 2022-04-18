<?php
require_once('../includes/resources/config.php');
	
$calculo = 60 * 60 * 24 * 30; //2592000 = 30 dias
$venceu = date("Y-m-d", time()-$calculo);
$venceu = "".$venceu." 00:00:00";

$calculo2 = 60 * 60 * 24 * 90; //7776000 = 90 dias
$venceu2 = date("Y-m-d", time()-$calculo2);
$venceu2 = "".$venceu2." 00:00:00";


	DB::exQuery("DELETE FROM `inlog_logs`");
	DB::exQuery("DELETE FROM `gebeurtenis` WHERE `gelezen`='1' AND `datum`<'".$venceu."'");
 	DB::exQuery("DELETE FROM `pokemon_wild_gevecht` WHERE `datainicio`<'".$venceu."'");
  	DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `datainicio`<'".$venceu."'");
  	DB::exQuery("DELETE FROM `aanval_log` WHERE `datainicio`<'".$venceu."'");
  	DB::exQuery("DELETE FROM `bank_logs` WHERE `date`<'".$venceu."'");  	
  	DB::exQuery("DELETE FROM `battle_logs` WHERE `date`<'".$venceu."'");
   	DB::exQuery("DELETE FROM `release_log` WHERE `date`<'".$venceu."'"); 	  	
	DB::exQuery("DELETE FROM `transferlist_log` WHERE `date`<'".$venceu."'"); 
	DB::exQuery("UPDATE `pokemon_speler` SET `opzak`='nee' WHERE `opzak`='tra'");
// 	DB::exQuery("DELETE FROM `transferlijst`");
	
	
	
	  $itemmrkt = DB::exQuery("select * from itemmarket");
     while($sobreoitem = $itemmrkt->fetch_assoc()) {
	
	if ($sobreoitem['soort'] == "tm") {
	DB::exQuery("UPDATE `gebruikers_tmhm` SET `".$sobreoitem['item']."`=`".$sobreoitem['item']."`+".$sobreoitem['qnt']." WHERE `user_id`='".$sobreoitem['user_id']."'");
	} else {
	DB::exQuery("UPDATE `gebruikers_item` SET `".$sobreoitem['item']."`=`".$sobreoitem['item']."`+".$sobreoitem['qnt']." WHERE `user_id`='".$sobreoitem['user_id']."'");
	}


	DB::exQuery("DELETE FROM `itemmarket` WHERE `id`='".$sobreoitem['id']."'");

	  }
	
	
	
  #Optimizar tabela.	
  DB::exQuery("OPTIMIZE TABLE `aanval`, `aanval_log`, `aanval_new`, `arrowchat`, `arrowchat_admin`, `arrowchat_applications`, `arrowchat_banlist`, `arrowchat_chatroom_banlist`, `arrowchat_chatroom_messages`, `arrowchat_chatroom_rooms`, `arrowchat_chatroom_users`, `arrowchat_config`, `arrowchat_graph_log`, `arrowchat_notifications`, `arrowchat_notifications_markup`, `arrowchat_smilies`, `arrowchat_status`, `arrowchat_themes`, `arrowchat_trayicons`, `ban`, `bank_logs`, `battle_logs`, `berichten`, `bovenstuk`, `casino`, `characters`, `clans`, `clan_invites`, `clan_profiel`, `configs`, `cron`, `daycare`, `duel`, `duel_logs`, `effect`, `experience`, `forum_berichten`, `forum_categorieen`, `forum_topics`, `gebeurtenis`, `gebruikers`, `gebruikers_badges`, `gebruikers_item`, `gebruikers_tmhm`, `home`, `huizen`, `inlog_fout`, `inlog_logs`, `items`, `karakters`, `kluis_kraken`, `league`, `league_award`, `league_battle`, `league_participant`, `levelen`, `logs`, `log_troca_email`, `log_troca_nick`, `log_troca_senha`, `loterij`, `loterij_kaarten`, `markt`, `marktespecial`, `moverecorder`, `news`, `nieuws`, `PagSeguroTransacoes`, `paymentez`, `pokemon_nieuw_baby`, `pokemon_nieuw_gewoon`, `pokemon_nieuw_starter`, `pokemon_speler`, `pokemon_speler_gevecht`, `pokemon_wild`, `pokemon_wild_gevecht`, `premium`, `rank`, `release_log`, `tmhm`, `tmhm_movetutor`, `tmhm_relacionados`, `toernooi`, `toernooi_inschrijving`, `toernooi_ronde`, `traders`, `trainer`, `trainer_pokemon`, `transferlijst`, `transferlist_log`, `voordeel`, `wwvergeten`");
  
  echo "Cron executado com sucesso.";
  
?>