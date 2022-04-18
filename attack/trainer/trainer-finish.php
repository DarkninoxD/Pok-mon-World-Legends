<?php
//Is all the information send
if ( (isset($_GET['aanval_log_id'])) && (isset($_GET['sid']))) {
  //Connect With Database
  include_once("../../app/includes/resources/config.php");
  //Include Default Functions
  include_once("../../app/includes/resources/ingame.inc.php");
  //Include Attack Functions
  include("../attack.inc.php"); 
  $page = 'attack/trainer/trainer-attack';
  //Goeie taal erbij laden voor de page
  include_once('../../language/language-pages.php');
  include_once('../../language/language-general.php');
  //Load User Information
  $gebruiker = DB::exQuery("SELECT * FROM `gebruikers`, `gebruikers_item` WHERE ((`gebruikers`.`user_id`='".$_SESSION['id']."') AND (`gebruikers_item`.`user_id`='".$_SESSION['id']."'))")->fetch_assoc();
  if ($gebruiker['itembox'] == 'bag')
    $gebruiker['item_over'] = 20-$gebruiker['items'];
  else if ($gebruiker['itembox'] == 'Yellow box')
    $gebruiker['item_over'] = 50-$gebruiker['items'];
  else if ($gebruiker['itembox'] == 'Blue box')
    $gebruiker['item_over'] = 100-$gebruiker['items'];
  else if ($gebruiker['itembox'] == 'Red box')
    $gebruiker['item_over'] = 250-$gebruiker['items'];	
  //Load Data
  $aanval_log = aanval_log($_GET['aanval_log_id']);
  //Test if fight is over
  if ($aanval_log['laatste_aanval'] == "end_screen") {
    if (DB::exQuery("SELECT `id` FROM `pokemon_speler_gevecht` WHERE `user_id`='".$gebruiker['user_id']."' AND `leven`>'0'")->num_rows == 0)
	{
      if ($gebruiker['rank'] >= 3) $money = round($gebruiker['silver']/50);
      else $money = 0;
      $win = 0;
      //Update user
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$money."', `verloren`=`verloren`+'1',`points`=if (`points` > 0, (`points` - 60), 0),`points_temp`=if (`points_temp` > 0, (`points_temp` - 60), 0) WHERE `user_id`='".$_SESSION['id']."'");
    }
    else
	{
      $win = 1;
      //Load Trainer Data
      $trainer = DB::exQuery("SELECT * FROM `trainer` WHERE `naam`='".$aanval_log['trainer']."'")->fetch_assoc();	
    	//HM Cut
    	if ($trainer['badge'] == 'Hive')
		{
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM01`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM01 Cut.';
    	}
    	//HM Fly
    	else if ($trainer['badge'] == 'Feather')
		{
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM02`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM02 Fly.';
    	}
    	//HM Surf
    	else if ($trainer['badge'] == 'Cascade') {
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM03`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM03 Surf.';
    	}
    	//HM Strength
    	else if ($trainer['badge'] == 'Knuckle') {
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM04`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM04 Strength.';
    	}
    	//HM Flash
    	else if ($trainer['badge'] == 'Relic') {
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM05`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM05 De fog.';
    	}
    	//HM Rock Smash
    	else if ($trainer['badge'] == 'Storm') {
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM06`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM06 Rock Smash.';
    	}
    	//HM Waterfall
    	else if ($trainer['badge'] == 'Fen') {
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM07`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM07 Waterfall.';
    	}
    	//HM Dive
    	else if ($trainer['badge'] == 'Rain') {
    		DB::exQuery("UPDATE `gebruikers_tmhm` SET `HM08`='1' WHERE `user_id`='".$_SESSION['id']."'");
    		$hm = $txt['you_also_get_hm'].' HM08 Rock Climb.';
    	}
  	
      //Give Badge
      if (!empty($trainer['badge'])) { 
        DB::exQuery("UPDATE `gebruikers_badges` SET `".$trainer['badge']."`='1' WHERE `user_id`='".$_SESSION['id']."'");
        $gym_w = $gebruiker['wereld'].'_gym';

        if ($gebruiker[$gym_w] == 7) { 
          $unlock_w = [
            'Kanto' => 'Johto',
            'Johto' => 'Hoenn',
            'Hoenn' => 'Sinnoh',
            'Sinnoh' => 'Unova',
            'Unova' => 'Kalos',
            'Kalos' => 'Alola',
            'Alola' => 'Kanto'
          ][$gebruiker['wereld']];
          $unlock_w2 = $unlock_w.'_block';
          DB::exQuery("UPDATE gebruikers SET badges = badges + '1', $gym_w = $gym_w + '1', $unlock_w2 = '1' WHERE user_id = '".$_SESSION['id']."'");

          $event = '<img src="public/images/icons/blue.png" width="16" height="16" class="imglower" /> Você conseguiu <b>todas</b> as Insíginias de <b>'.$gebruiker['wereld'].'</b> e desbloqueou o acesso à uma <b>NOVA REGIÃO</b>: '.$unlock_w.'!';

          DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $_SESSION['id'] . "', '" . $event . "', '0')");
        } else {
          DB::exQuery("UPDATE gebruikers SET badges = badges + '1', $gym_w = $gym_w + '1' WHERE user_id = '".$_SESSION['id']."'");
        }
      	rankerbij('gym',$txt);
      }								
      else{
        #miss query van Gold + 1
        rankerbij('trainer',$txt);
      }
      //Give money
      $quests->setStatus('win_npc', $_SESSION['id']);
      $money = round($trainer['prijs']*(rand(95,(110+$gebruiker['rank']+20))/20));
      $valorsilvertrainer = DB::exQuery("SELECT * FROM configs WHERE config='silver'")->fetch_assoc();
      $money = $money * $valorsilvertrainer['valor'];
      DB::exQuery("UPDATE `gebruikers` SET `gewonnen`=`gewonnen`+1,`silver`=`silver`+'".$money."',`points`=(`points`+100),`points_temp`=(`points_temp`+100) WHERE `user_id`='".$_SESSION['id']."'");
      //Maybe Give badge case
      if ($gebruiker['Badge case'] == 0)
        DB::exQuery("UPDATE `gebruikers_item` SET `Badge case`='1' WHERE `user_id`='".$_SESSION['id']."'");
    }

    if ($trainer['naam'] == 'Jessie e James') {
      $hm = '<br> Equipe Rocket decolando denovo!';
    }
      
    echo $trainer['badge']." | ".$money ." | ".$rarecandy." | ".$hm." | ".$win;
    //Sync pokemon
    pokemon_player_hand_update();
    //Let Pokemon grow
    pokemon_grow($txt);
    //Remove Attack
    remove_attack($_GET['aanval_log_id']);
  }
  else
  {
    header("Location: ./attack/trainer/trainer-attack");
  }
}
?>