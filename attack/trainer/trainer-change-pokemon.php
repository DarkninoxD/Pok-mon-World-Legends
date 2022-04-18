<?php
//Check if all information is send.
if ((isset($_GET['pokemon_info_name'])) && (isset($_GET['computer_info_name'])) && (isset($_GET['aanval_log_id'])) && (isset($_GET['sid']))) { 
  //Connect With Database
  include_once("../../app/includes/resources/config.php");
  //Include Default Functions
  include_once("../../app/includes/resources/ingame.inc.php");
  //Include Attack Functions
  include("../attack.inc.php"); 
  $page = 'attack/trainer/trainer-attack';
  //Goeie taal erbij laden voor de page
  include_once('../../language/language-pages.php');
  //Load Attack Info
  $aanval_log = aanval_log($_GET['aanval_log_id']);
  //Check if the right aanval_log is choosen
  if ($aanval_log['user_id'] != $_SESSION['id']) exit;
  if ($_SESSION['sec_key'] != $_GET['_h'])	exit;
  //Load Computer Info
  $pokemon_info = pokemon_data($aanval_log['pokemonid']);
  $pokemon_info['naam_goed'] = addslashes(pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam'],$pokemon_info['icon']));
  //Default refresh 0
  $refresh = 0;
  //Check if trainer has to change
  if ($aanval_log['laatste_aanval'] == "trainer_wissel") {
    $new_computer = DB::exQuery("SELECT pokemon_wild.naam, pokemon_wild.wild_id, pokemon_wild_gevecht.id, pokemon_wild_gevecht.levenmax, pokemon_wild_gevecht.leven, pokemon_wild_gevecht.speed, pokemon_wild_gevecht.effect FROM pokemon_wild INNER JOIN pokemon_wild_gevecht ON pokemon_wild.wild_id = pokemon_wild_gevecht.wildid WHERE `aanval_log_id`='".$_GET['aanval_log_id']."' AND `leven`>'0' ORDER BY rand() limit 1")->fetch_assoc();
    $new_computer['naam_goed'] = computer_naam($new_computer['naam']);
    $message = $aanval_log['trainer']." ".$txt['bringed']." ".$new_computer['naam_goed'].".<br />";
    if ($pokemon_info['speed'] > $new_computer['speed']) {
      $message .= $txt['your_turn'];
      $lastmove = "computer";
    }
    else{
      $message .= $new_computer['naam_goed']." ".$txt['opponents_turn'];
      $lastmove = "pokemon";
      $refresh = 1;
    }

    //Update aanval_log
    DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='".$lastmove."', `tegenstanderid`='".$new_computer['id']."' WHERE `id`='".$aanval_log['id']."'");
    //Save Computer As Seen in Pokedex
    update_pokedex($new_computer['wild_id'],'','zien');

    echo $message." | ".$new_computer['naam']." | ".$new_computer['leven']." | ".$new_computer['levenmax']." | ".$refresh ." | ".$aanval_log['tegenstanderid']." | ".$new_computer['wild_id']." | ".$new_computer['effect'];
  }
  else echo "Error: 5001";
}
?>