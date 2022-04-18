<?php
//Is all the information send
if ((isset($_GET['item'])) && (isset($_GET['sid'])) && (isset($_GET['aanval_log_id'])) && (isset($_GET['option_id'])) && (isset($_GET['computer_info_name']))) {
  //Connect With Database
  include_once("../../app/includes/resources/config.php");
  //Include Attack Functions
  include("../attack.inc.php");
  //Include Default Functions
  include_once("../../app/includes/resources/ingame.inc.php");
  $page = 'attack/wild/wild-attack';
  //Goeie taal erbij laden voor de page
  include_once('../../language/language-pages.php');
  //Load Attack Info
  $aanval_log = aanval_log($_GET['aanval_log_id']);
  //Check if the right aanval_log is choosen
  if ($aanval_log['user_id'] != $_SESSION['id']) exit;
  if ($_SESSION['sec_key'] != $_GET['_h']) exit;
  if ($_SESSION['caught'] == 1) { $message = "Algo deu errado."; echo $message; } else {
  //Load Computer Info
  $computer_info = computer_data($aanval_log['tegenstanderid']);
  $computer_info['naam_goed'] = computer_naam($computer_info['naam']);
  //Load Player item info
  $player_item_info = DB::exQuery("SELECT `Poke ball`, `Great ball`, `Ultra ball`, `Premier ball`, `Net ball`, `Dive ball`, `Nest ball`, `Repeat ball`, `Timer ball`, `Master ball`, `Moon ball`, `Dusk ball`, `Dream ball`, `Luxury ball`, `Rocket ball`, `DNA ball`, `Cherish ball`, `Black ball`, `Santa ball`, `Antique ball`, `Frozen ball`, `GS ball`, `Trader ball`, `Ecology ball` FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
  $gegeven = DB::exQuery("SELECT `huis` FROM `gebruikers` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
  //Load pokeball info
  $item_info = DB::exQuery("SELECT `naam`, `wat`, `kracht`, `apart`, `type1`, `type2`, `type3`, `kracht2` FROM `items` WHERE `naam`='".$_GET['item']."'")->fetch_assoc();
  //Get opzak_nummer from last pokemon
  $player_hand = DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' and `opzak` = 'ja'")->num_rows;
  //$player_hand = mysql_fetch_array(DB::exQuery("SELECT `opzak_nummer` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' ORDER BY `opzak_nummer` DESC LIMIT 0,1"));
  //Count pokmon in House
  $house = DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='nee'")->num_rows;
  //Set default good on one
  $good = 1;
  $drp = 0;
  //Check Amount of pokemon that can hold the house
  if ($gegeven['huis'] == "doos") { $over = 2-$house; }
  else if ($gegeven['huis'] == "shuis") { $over = 20-$house; }
  else if ($gegeven['huis'] == "nhuis") { $over = 100-$house; }
  else if (($gegeven['huis'] == "villa") OR ($gegeven['huis'] == "Villa")) { $over = 2500-$house; }
  //Check if it is an valid item
  if ($_GET['item'] == "Kies") $message = $txt['ball_choose'];
  //Check if it is a pokeball
  else if ($item_info['wat'] != "pokeball") $message = $txt['ball_have'];
  //Check if you have that pokeball
  else if ($player_item_info[$_GET['item']] <= "0") $message = $txt['ball_amount'].$_GET['item'].".";
  //Check if Hand AND House is full
  else if (($player_hand > 5) AND ($over <= 0)) $message = $txt['hand_house_full'];
  //Check if you have already caught the computer
  else if ($aanval_log['laatste_aanval'] == "gevongen") $message = $txt['success_catched_1'].$computer_info['naam_goed'].$txt['success_catched_2'];
  //Check if the fight is finished yet
  else if ($aanval_log['laatste_aanval'] == "klaar") $message = $txt['new_pokemon_dead_1'].$computer_info['naam_goed'].$txt['new_pokemon_dead_2'];
  //Check if it is not your turn
  else if ($aanval_log['laatste_aanval'] == "pokemon") $message = $computer_info['naam_goed'].$taal['attack']['general']['lastattack'];
  //Alle Checks done, Try catching the pokemon
  else{
    //Masterball has been used. Pokemon is caught
    if ($item_info['naam'] == "Master ball") $catched = true;
    else if ($item_info['naam'] == "DNA ball") $catched = true;
    else if ($item_info['naam'] == "Cherish ball") $catched = true;
    else if ($item_info['naam'] == "Black ball") $catched = true;
    else if ($item_info['naam'] == "Santa ball") $catched = true;
    else if ($item_info['naam'] == "GS ball") $catched = true;
    else{
      //No masterball calculate catch
      $catched = false;
      //Computer has an effect
      if ($_GET['computer_effect'] != "") {
        //Load Computer Effect
        $effect_info = DB::exQuery("SELECT `vangkans` FROM `effect` WHERE `actie`='".$computer_info['effect']."'")->fetch_assoc();
        //Effect Catch change
        $catch_change = $effect_info['vangkans'];
        if (($catch_change < "0.5")) $catch_change = 1;
      }
      //Computer has no effect
      //Effect Catch change
      else $catch_change = 1;
      
      //Calculate Power of pokeball
      //Is the pokeball not strange
      if ($item_info['apart'] == "nee") $pokeball_power = $item_info['kracht'];
      //Pokeball is strange
      else if ($item_info['apart'] == "ja") {
      
      if ($item_info['type2'] == "") $item_info['type2'] = "Nenhum";
      
        //Is it a Net ball
        if ($item_info['naam'] == "Net ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //Check if Type 1 or Type 2 is the same
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No Match, Default power
          else $pokeball_power = $item_info['kracht'];
        }
        else if ($item_info['naam'] == "Antique ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }        
        else if ($item_info['naam'] == "Frozen ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }             
        //Is it a Dive ball
        else if ($item_info['naam'] == "Dive ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }    
         //Is it a Dream ball
        else if ($item_info['naam'] == "Dream ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }
        //Is it a Dusk ball
        else if ($item_info['naam'] == "Dusk ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }
        else if ($item_info['naam'] == "Ecology ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }
          //Is it a Moon ball
        else if ($item_info['naam'] == "Moon ball") {
          //Check if Type 1 or Type 2 is the same
          if (($computer_info['type1'] == $item_info['type1']) OR ($computer_info['type1'] == $item_info['type2']) OR ($computer_info['type1'] == $item_info['type3'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          else if (($computer_info['type2'] == $item_info['type1']) OR ($computer_info['type2'] == $item_info['type2']) OR ($computer_info['type2'] == $item_info['type3'])) {
            $pokeball_power = $item_info['kracht2'];
          }
          //No match
          else $pokeball_power = $item_info['kracht'];
        }
        //Is it A Nest ball
        else if ($item_info['naam'] == "Nest ball") {
          //Check if the computer level is between 1 and 19.
          if (($computer_info['level'] >= 1) AND ($computer_info['level'] <= 19)) {
            $pokeball_power = "3.0";
          }
          //Check if the computer level is between 20 and 29
          else if (($computer_info['level'] >= 20) AND ($computer_info['level'] <= 29)) {
            $pokeball_power = "2.0";
          }
          //Is the level higher dan 30, then default power
          else $pokeball_power = $item_info['kracht'];
        }
        //Is it a repeat ball
        else if ($item_info['naam'] == "Repeat ball") {
          //Check if player already have this pokemon
          if (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `wild_id`='".$computer_info['wildid']."' AND `user_id`='".$_SESSION['id']."'")->num_rows >= 1) {
            $pokeball_power = $item_info['kracht2'];
          }
          //Player don't have this pokemon yet
          else $pokeball_power = $item_info['kracht'];
        }
        //Is it a timer ball
        else if ($itemgegevens['naam'] == "Timer ball") {
          //Have there been made more than 30 moves
          if ($aanval_log['beurten'] >= 30) $pokeball_power = "4.0";
          //Have there been between 20 and 29 moves
          else if (($aanval_log['beurten'] >= 20) AND ($aanval_log['beurten'] <= 29)) {
            $pokeball_power = "3.0";
          }
          //Have there been between 10 and 19 moves
          else if (($aanval_log['beurten'] >= 10) AND ($aanval_log['beurten'] <= 19)) {
            $pokeball_power = "2.0";
          }
          //less then 10 moves
          else $pokeball_power = $item_info['kracht'];
        }
        //Oops Error!
        else $message = "Error: 2001";
      }
    }

    //Check if masterball is been used
    if (!$catched) {   
      //Calculate Catch Rate
      $uitkomst3 = (((((3*$computer_info['levenmax'])-(2*$computer_info['leven']))*$computer_info['vangbaarheid'])*$pokeball_power)/(3*$computer_info['levenmax']))*$catch_change;
      if (255 <= $uitkomst3) $catched = true;
      else{
        if ($uitkomst3 == 0) $uitkomst3 = 1;
        $uitkomst444 = 16711680/$uitkomst3;
        $uitkomst44 = sqrt(sqrt($uitkomst444));
        if ($uitkomst44 == 0) $uitkomst44 = 1;
        $uitkomst4 = floor(1048650/$uitkomst44);
        //Manipulatie
        $getal1 = rand(40000,65535);
        //Gewone berekening
        $getal2 = rand(0,65535);
        $getal3 = rand(0,65535);
        $getal4 = rand(0,65535);
        if (($uitkomst4 >= $getal1) AND ($uitkomst4 >= $getal2) AND ($uitkomst4 >= $getal3) AND ($uitkomst4 >= $getal4)) {
          $catched = true;
        }
        else $catched = false;
      }
    }
    
    //Pokeball is gone
    DB::exQuery("UPDATE `gebruikers_item` SET `".$item_info['naam']."`=`".$item_info['naam']."`-'1' WHERE `user_id`='".$_SESSION['id']."'");
 
    //Pokemon is Caught
    if ($catched) {
      //Save Computer As Seen in Pokedex
      update_pokedex($computer_info['wild_id'],'','vangen');
      //Choose Character 
      $character = DB::exQuery("SELECT * FROM `karakters` ORDER BY rand() limit 1")->fetch_assoc();
      $new_pokemon['karakter'] = $character['karakter_naam'];

      //Increase total pokemon in hand with one
      $player_handn = $player_hand+1;
      
      //Load Exp Info
      $computer_info['levell'] = $computer_info['level']+1;
      $experience_info = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='".$computer_info['groei']."' AND `level`='".$computer_info['levell']."'")->fetch_assoc();
      $new_pokemon['expnodig'] = $experience_info['punten'];
    
      //Create Pokemon IV
      $new_pokemon['attack_iv']     = rand(0,31);
      $new_pokemon['defence_iv']    = rand(0,31);
      $new_pokemon['speed_iv']      = rand(0,31);
      $new_pokemon['spcattack_iv']  = rand(0,31);
      $new_pokemon['spcdefence_iv'] = rand(0,31);
      $new_pokemon['hp_iv']         = rand(0,31);
      
      //Calculate Stats
      //Formule Stats = int((int(int(A*2+B+int(C/4))*D/100)+5)*E)
      $new_pokemon['attackstat']     = round((((($computer_info['attack_base']*2+$new_pokemon['attack_iv'])*$computer_info['level']/100)+5)*1)*$character['attack_add']);
      $new_pokemon['defencestat']    = round((((($computer_info['defence_base']*2+$new_pokemon['defence_iv'])*$computer_info['level']/100)+5)*1)*$character['defence_add']);
      $new_pokemon['speedstat']      = round((((($computer_info['speed_base']*2+$new_pokemon['speed_iv'])*$computer_info['level']/100)+5)*1)*$character['speed_add']);
      $new_pokemon['spcattackstat']  = round((((($computer_info['spc.attack_base']*2+$new_pokemon['spcattack_iv'])*$computer_info['level']/100)+5)*1)*$character['spc.attack_add']);
      $new_pokemon['spcdefencestat'] = round((((($computer_info['spc.defence_base']*2+$new_pokemon['spcdefence_iv'])*$computer_info['level']/100)+5)*1)*$character['spc.defence_add']);
      $new_pokemon['hpstat']         = round(((($computer_info['hp_base']*2+$new_pokemon['hp_iv'])*$computer_info['level']/100)+$computer_info['level'])+10);

      $message = $txt['ball_throw_1'].$item_info['naam'].$txt['ball_throw_2'].$computer_info['naam_goed'].$txt['ball_success'];
      //Succes
      $good = 0;

      $date = date('Y-m-d H:i:s');
      
      //Check if Player hand is full
      if ($player_handn > 6) {
        //Save New Pokemon
        DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`, `user_id`, `opzak`, `karakter`, `shiny`, `level`, `levenmax`, `leven`, `expnodig`, `attack`, `defence`, `speed`, `spc.attack`, `spc.defence`, `attack_iv`, `defence_iv`, `speed_iv`, `spc.attack_iv`, `spc.defence_iv`, `hp_iv`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `effect`, `gevongenmet`, `ability`, `capture_date`) 
          SELECT `wildid`, '".$_SESSION['id']."', 'nee', '".$new_pokemon['karakter']."', '".$computer_info['shiny']."', '".$computer_info['level']."', '".$new_pokemon['hpstat']."', '".$computer_info['leven']."', '".$new_pokemon['expnodig']."', '".$new_pokemon['attackstat']."', '".$new_pokemon['defencestat']."', '".$new_pokemon['speedstat']."', '".$new_pokemon['spcattackstat']."', '".$new_pokemon['spcdefencestat']."', '".$new_pokemon['attack_iv']."', '".$new_pokemon['defence_iv']."', '".$new_pokemon['speed_iv']."', '".$new_pokemon['spcattack_iv']."', '".$new_pokemon['spcdefence_iv']."', '".$new_pokemon['hp_iv']."', '".$computer_info['aanval_1']."', '".$computer_info['aanval_2']."', '".$computer_info['aanval_3']."', '".$computer_info['aanval_4']."', `effect`, '".$item_info['naam']."', '".$computer_info['ability']."', '".$date."'  FROM `pokemon_wild_gevecht` WHERE `id`='".$computer_info['id']."'");
        $message .= $computer_info['naam_goed'].$txt['ball_success_2'];
     }
      //Player hand is not full
      else{
        //Save New pokemon
        DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`, `user_id`, `opzak`, `opzak_nummer`, `karakter`, `shiny`, `level`, `levenmax`, `leven`, `expnodig`, `attack`, `defence`, `speed`, `spc.attack`, `spc.defence`, `attack_iv`, `defence_iv`, `speed_iv`, `spc.attack_iv`, `spc.defence_iv`, `hp_iv`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `effect`, `gevongenmet`, `ability`, `capture_date`) 
          SELECT `wildid`, '".$_SESSION['id']."', 'ja', '".$player_handn."', '".$new_pokemon['karakter']."', '".$computer_info['shiny']."', '".$computer_info['level']."', '".$new_pokemon['hpstat']."', '".$computer_info['leven']."', '".$new_pokemon['expnodig']."', '".$new_pokemon['attackstat']."', '".$new_pokemon['defencestat']."', '".$new_pokemon['speedstat']."', '".$new_pokemon['spcattackstat']."', '".$new_pokemon['spcdefencestat']."', '".$new_pokemon['attack_iv']."', '".$new_pokemon['defence_iv']."', '".$new_pokemon['speed_iv']."', '".$new_pokemon['spcattack_iv']."', '".$new_pokemon['spcdefence_iv']."', '".$new_pokemon['hp_iv']."', '".$computer_info['aanval_1']."', '".$computer_info['aanval_2']."', '".$computer_info['aanval_3']."', '".$computer_info['aanval_4']."', `effect`, '".$item_info['naam']."', '".$computer_info['ability']."', '".$date."' FROM `pokemon_wild_gevecht` WHERE `id`='".$computer_info['id']."'");
      }
      
      //Increase pokemon amount of player, make last visited page empty
      $quests->setStatus('catch', $_SESSION['id']);
      $quests->setStatus('catch_single', $_SESSION['id'], $computer_info['wild_id']);

      if (!empty($evento_atual)) {
        if ($evento_atual['name_id'] == 'pikachu_festival') {
          $drop = drops($computer_info['wild_id'], $computer_info['shiny']);
          if ($drop > 0) {
              $qtd = $drop;
              $name = '1_drop';
              $drp = 'TOKEN PIKACHU,'.$qtd;
              DB::exQuery("UPDATE `gebruikers` SET `$name`=`$name`+'$qtd' WHERE `user_id`='".$_SESSION['id']."'");
          }
        }
      }
      
      DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='".$_SESSION['id']."'");
      $_SESSION['caught']= 1;
      //Sync pokemon
      pokemon_player_hand_update();
      //Remove Attack
      remove_attack($_GET['aanval_log_id']);
    }
    else{
      //Last move is player
      DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='pokemon' WHERE `id`='".$aanval_log['id']."'");  
 
      $message = $txt['ball_throw_1'].$item_info['naam'].$txt['ball_throw_2'].$computer_info['naam_goed'].$txt['ball_failure'];
    }
  }
  //Create info to sent back
  $info_ball_left = $player_item_info[$item_info['naam']]-1;
  echo $message." | ".$good." | ".$info_ball_left." | ".$_GET['option_id']." | ".$item_info['naam']." | Pokeball | ".$drp;
}
}
?>