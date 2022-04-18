<?php
 //Is all the information send
if ( (isset($_GET['aanval_log_id'])) && (isset($_GET['sid']))) {
  //Connect With Database
  include_once("../../app/includes/resources/config.php");
  //Include Default Functions
  include_once("../../app/includes/resources/ingame.inc.php");
  //Include Attack Functions
  include("../attack.inc.php"); 
  //Goeie taal erbij laden voor de page
  include_once('../../language/language-general.php');
  //Load Data
  $aanval_log = aanval_log($_GET['aanval_log_id']);
  //Load User Information
  $gebruiker = DB::exQuery("SELECT * FROM `gebruikers`, `gebruikers_item` WHERE ((`gebruikers`.`user_id`='".$_SESSION['id']."') AND (`gebruikers_item`.`user_id`='".$_SESSION['id']."'))")->fetch_assoc();
  //Load computer info
  $computer_info = computer_data($aanval_log['tegenstanderid']);
  //Test if fight is over
  $drp = 0;

  if ($aanval_log['laatste_aanval'] == "end_screen") {
		if ($computer_info['leven'] <= 0) {
      rankerbij('attack',$txt);  
      //Update User
      DB::exQuery("UPDATE `gebruikers` SET `gewonnen`=`gewonnen`+'1',`in_battle`=0,`map_wild`=0,`points`=(`points` + 50),`points_temp`=(`points_temp` + 50) WHERE `user_id`='".$_SESSION['id']."'");
           
      if (!empty($evento_atual)) {
          if ($evento_atual['category'] == 'drop') {
            $drop = getDrop($aanval_log['gebied']);
            if (!empty($drop)) {
              $per = drop($drop['id'], $drop['chance']);
              if ($per) {
                $qtd = drops($drop['id']);
                $name = $drop['id'].'_drop';
                $drp = $drop['name'].','.$qtd;
                DB::exQuery("UPDATE `gebruikers` SET `$name`=`$name`+'$qtd' WHERE `user_id`='".$_SESSION['id']."'");
              }
            }
          } else if ($evento_atual['name_id'] == 'pikachu_festival') {
            $drop = drops($computer_info['wild_id'], $computer_info['shiny']);
            if ($drop > 0) {
                $qtd = $drop;
                $name = '1_drop';
                $drp = 'TOKEN PIKACHU,'.$qtd;
                DB::exQuery("UPDATE `gebruikers` SET `$name`=`$name`+'$qtd' WHERE `user_id`='".$_SESSION['id']."'");
            }
        }
      }
			
      $text = 1;
      $money = 0;
    }
    else{
      if ($gebruiker['rank'] >= 3) $money = round($gebruiker['silver']/10);
      else $money = 0;
      //Update user
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$money."', `verloren`=`verloren`+'1',`points`=if (`points` > 0, (`points` - 30), 0),`points_temp`=if (`points_temp` > 0, (`points_temp` - 30), 0) WHERE `user_id`='".$_SESSION['id']."'");
      $text = 0;
    }
    
    echo $text." | ".$money." | ".$drp;
    //Sync pokemon
    pokemon_player_hand_update();
    //Let Pokemon grow
    pokemon_grow($txt);
    //Remove Attack
    remove_attack($_GET['aanval_log_id']);
    unset($_SESSION['attack']['aanval_log_id']);
    unset($_SESSION['caught']);
    setcookie('market_battle_used', '', (3600 * -1));
  }
  else
  {
    header("Location: ./map");
  }
}
?>