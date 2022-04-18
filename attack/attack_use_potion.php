<?php

//Is all the information send

if ((isset($_GET['item'])) && (isset($_GET['sid'])) && (isset($_GET['aanval_log_id'])) && (isset($_GET['option_id'])) && (isset($_GET['potion_pokemon_id'])) && (isset($_GET['computer_info_name']))) {
  //Connect With Database

  include_once("../app/includes/resources/config.php");

  //Include Default Functions

  include_once("../app/includes/resources/ingame.inc.php");

  //Include Attack Functions

  include_once("attack.inc.php");

  //Include Attack Lang

  $page = 'attack/trainer/trainer-attack';

  //Goeie taal erbij laden voor de page

  include_once('../language/language-pages.php');

  //Load Attack Info

  $aanval_log = aanval_log($_GET['aanval_log_id']);

  //Check if the right aanval_log is choosen

  if ($aanval_log['user_id'] != $_SESSION['id']) exit;
  if ($_SESSION['sec_key'] != $_GET['_h'])	exit;

  //Load Pokemon info

  $pokemon_info = pokemon_data($_GET['potion_pokemon_id']);

  //Check if the right pokemon is choosen

  if ($pokemon_info['user_id'] != $_SESSION['id']) exit;

  //Change Pokemon Name

  $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);

  //Load Player item info

  $player_item_info = DB::exQuery("SELECT `Potion`, `Super potion`, `Hyper potion`, `Full heal`, `Revive`, `Max revive`, `Moomoo Milk`, `Fresh Water`, `Soda Pop`, `Lemonade` FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();

  //Load pokeball info

  $item_info = DB::exQuery("SELECT `naam`, `wat`, `kracht`, `apart`, `type1`, `type2`, `kracht2` FROM `items` WHERE `naam`='".$_GET['item']."'")->fetch_assoc();

  //Potion was no succes

  $good = 0;

  //Create Right name for computer

  $_GET['computer_info_name'] = computer_naam($_GET['computer_info_name']);

  //Check if it is an valid item

  if ($_GET['item'] == "Kies") $message = $txt['potion_choose'];

  //Check if it is a potion

  else if ($item_info['wat'] != "potion") $message = $txt['potion_have'];

  //Check if you have that pokeball

  else if ($player_item_info[$item_info['naam']] <= 0) $message = $txt['potion_amount'].$_GET['item'].".";

  //Pokemon has full life

  else if (in_array($item_info['naam'], array('Revive', 'Max revive')) && $pokemon_info['leven'] > 0) $message = $pokemon_info['naam'].' não pode ser revivido pois ainda tem HP!';
  
  else if ($pokemon_info['leven'] <= 0 && !in_array($item_info['naam'], array('Revive', 'Max revive'))) $message = $pokemon_info['naam_goed'].' não tem HP para receber Poções!';

  else if ($pokemon_info['leven'] >= $pokemon_info['levenmax']) $message = $pokemon_info['naam_goed'].' está com HP cheio!';

  //Check if the fight is finished yet

  else if ($aanval_log['laatste_aanval'] == "klaar") $message = $txt['dead_1'].$_GET['computer_info_name'].$txt['dead_2'];

  //Check if it is not your turn

  else if ($aanval_log['laatste_aanval'] == "pokemon") $message = $_GET['computer_info_name'].$taal['attack']['general']['lastattack'];

  //Use Potion

  else{

    //Effect Info

    $pokemon_effect = $pokemon_info['effect'];

    $new_amount = $player_item_info[$item_info['naam']];

    

    switch($item_info['apart']) {

      //Item isn't strange

      case "nee":

        //Pokemon is dead, potions don't work

        if ($pokemon_info['leven'] <= 0) $message = $pokemon_info['naam_goed'].' não tem HP para usar Poções!';

        //pokemon is not dead

        else{ 

          $message = $txt['potion_give_1'].$pokemon_info['naam_goed'].$txt['potion_give_2'].$item_info['naam'].".";

          //Calculate New life

          $new_life = $pokemon_info['leven']+$item_info['kracht'];

          //If new life is bigger than life max, new life becomes lifemax

          if ($new_life > $pokemon_info['levenmax']) $new_life = $pokemon_info['levenmax'];

          //Set new amount

          $new_amount -= 1;

        }

      break;

      

      //Item is strange

      case "ja":

        //Its a full heal

        if ($item_info['naam'] == "Full heal") {

          //Effect is empty

          $pokemon_effect = "";

          $new_life = $pokemon_info['levenmax'];

          $message = $txt['potion_give'].$item_info['naam'].". ".$pokemon_info['naam'].$txt['potion_give_end_1'];

        }

        //Its a Revive

        else if ($item_info['naam'] == "Revive") {

          //Calculate new life

          $new_life = round($pokemon_info['levenmax']/2);

          $message = $txt['potion_give'].$item_info['naam'].". ".$pokemon_info['naam'].$txt['potion_give_end_2'];  

        }

        //Its a max revive

        else if ($item_info['naam'] == "Max revive") {

          //Calculate new life

          $new_life = $pokemon_info['levenmax'];

          $message = $txt['potion_give'].$item_info['naam'].". ".$pokemon_info['naam'].$txt['potion_give_end_3'];  

        }

        //Set new amount

        $new_amount -= 1;

      break;    

      

      default:

        $message = "Error: 3001";  

    }

    //Potion was a succes

    $good = 1;

    //Save new life

    DB::exQuery("UPDATE `pokemon_speler_gevecht` SET `leven`='".$new_life."', `effect`='".$pokemon_effect."' WHERE `id`='".$pokemon_info['id']."'");

    //Update aanval log

    DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='pokemon' WHERE `id`='".$aanval_log['id']."'");

    //Remove Potion

    DB::exQuery("UPDATE `gebruikers_item` SET `".$item_info['naam']."`='".$new_amount."' WHERE `user_id`='".$_SESSION['id']."'");

  }

  

  //Create info to sent back

  $info_potion_left = $player_item_info[$item_info['naam']]-1;

  if ($aanval_log['pokemonid'] == $pokemon_info['id']) $pokemon_infight = 1;

  else $pokemon_infight = 0;

  echo ucfirst($message)." | ".$good." | ".$info_potion_left." | ".$_GET['option_id']." | ".$item_info['naam']." | Potion | ".$new_life." | ".$pokemon_info['levenmax']." | ".$pokemon_infight." | ".$pokemon_info['opzak_nummer']." | ".$pokemon_info['naam_goed']." | ".$pokemon_info['id'];

}

?>