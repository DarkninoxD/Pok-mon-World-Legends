<?php
if ((isset($_GET['duel_id'])) AND (isset($_GET['sid']))) {
  //Connect With Database
  include_once("../../app/includes/resources/config.php");
  //Include Default Functions
  include_once("../../app/includes/resources/ingame.inc.php");
  //Include Duel Functions
  include_once("duel.inc.php");
  //Include Attack Functions
  include_once("../../attack/attack.inc.php");
  //Load language
  $page = 'attack/duel/duel-attack';
  //Goeie taal erbij laden voor de page
  include_once('../../language/language-pages.php');
  //Load Duel Data
  $duel_sql = DB::exQuery("SElECT `id`, `uitdager`, `tegenstander`, `u_pokemonid`, `t_pokemonid`, `laatste_beurt_tijd`, `laatste_beurt`, `laatste_aanval`,`laatste_aanval2`, `schade`, `volgende_beurt`, `last_pokemon_id` FROM `duel` WHERE `id`='".$_GET['duel_id']."'");

  //Default text
  $refresh = 0;
  $info1 = "";
  $info2 = "";
  $info3 = "";
  $info4 = "";
  $info5 = "";
  $info6 = "";
  $effect = "";
  $effect2 = "";

  //If there is no duel
  if ($duel_sql->num_rows == 1) {
    $duel_info = $duel_sql->fetch_assoc();
    $time_left = strtotime(date("Y-m-d H:i:s"))-$duel_info['laatste_beurt_tijd'];
    if ($time_left > 121) {
      if ($duel_info['uitdager'] == $duel_info['volgende_beurt']) $winner = $duel_info['tegenstander'];
      else if ($duel_info['tegenstander'] == $duel_info['volgende_beurt']) $winner = $duel_info['uitdager'];
      $mes = $txt['opponent_too_late'];
      DB::exQuery("UPDATE `duel` SET `winner`='".$winner."' WHERE `id`='".$duel_info['id']."'");
      $refresh = 2;
    }
    else{
      if ($duel_info['uitdager'] == $_SESSION['naam']) {
        if ($duel_info['laatste_beurt'] == $duel_info['tegenstander']) {
          if ($duel_info['laatste_aanval'] == "wissel") {
            $pokemon_info = pokemon_data($duel_info['t_pokemonid']);
            $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);

            $info1 = $pokemon_info['wild_id'];
            $info2 = $pokemon_info['naam_goed'];
            $info3 = $pokemon_info['shiny'];
            $info4 = $pokemon_info['leven'];
            $info5 = $pokemon_info['levenmax'];
            $info6 = $duel_info['last_pokemon_id'];
            $effect = $pokemon_info['effect'];
            if ($duel_info['volgende_beurt'] == $duel_info['uitdager']) {
              $refresh = 1;
              $mes = "Seu oponente trocou de Pokémon. Seu turno.";
            }
            else{
              $refresh = 3;
              $mes = "Seu oponente trocou de Pokémon. Turno do oponente.";
            }
          }

          else if ($duel_info['volgende_beurt'] == $_SESSION['naam']) {
            $pokemon_info = pokemon_data($duel_info['u_pokemonid']);
            $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);
            $opponent_info = pokemon_data($duel_info['t_pokemonid']);
            $opponent_info['naam_goed'] = pokemon_naam($opponent_info['naam'],$opponent_info['roepnaam']);
            $refresh = 1;
            $info1 = $pokemon_info['leven'];
            $info2 = $pokemon_info['levenmax'];
            $info3 = $pokemon_info['naam_goed'];
            $info4 = $pokemon_info['opzak_nummer'];
            $effect = $pokemon_info['effect'];
            $effect2 = $opponent_info['effect'];
            if ($pokemon_info['leven'] <= 0) {                                                      
              $mes = $opponent_info['naam_goed']." ".$txt['did']." ".$duel_info['laatste_aanval'].'. Seu Pokémon foi derrotado. <br>Você deve trocar de Pokémon!';
              $refresh = 4;
            }
            else{
              $mes = $opponent_info['naam_goed']." ".$txt['did']." ".$duel_info['laatste_aanval'].". ".$txt['your_turn'];
              $refresh = 1;
            }
          }

          else if ($duel_info['volgende_beurt'] == "end_screen") {
            $pokemon_info = pokemon_data($duel_info['u_pokemonid']);
            $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);
            $opponent_info = pokemon_data($duel_info['t_pokemonid']);
            $opponent_info['naam_goed'] = pokemon_naam($opponent_info['naam'],$opponent_info['roepnaam']);
            $refresh = 1;
            $info1 = $pokemon_info['leven'];
            $info2 = $pokemon_info['levenmax'];
            $effect2 = $opponent_info['effect'];
            $refresh = 5;
            $mes = $opponent_info['naam_goed']." ".$txt['did']." ".$duel_info['laatste_aanval'].'<br> Você foi derrotado.';
          }
          else{
            $mes = "Error: 1101<br />Info: ".$duel_info['volgende_beurt'];
          }
        }
      }  
      else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
        if ($duel_info['laatste_beurt'] == $duel_info['uitdager']) {
          if ($duel_info['laatste_aanval'] == "wissel") {
            $pokemon_info = pokemon_data($duel_info['u_pokemonid']);
            $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);

            $info1 = $pokemon_info['wild_id'];
            $info2 = $pokemon_info['naam_goed'];
            $info3 = $pokemon_info['shiny'];
            $info4 = $pokemon_info['leven'];
            $info5 = $pokemon_info['levenmax'];
            $info6 = $duel_info['last_pokemon_id'];
            $effect = $pokemon_info['effect'];

            if ($duel_info['volgende_beurt'] == $duel_info['tegenstander']) {
              $refresh = 1;
              $mes = "Seu oponente trocou de Pokémon. Seu turno.";
            }
            else{
              $refresh = 3;
              $mes = "Seu oponente trocou de Pokémon. Turno do oponente.";
            }
          }
          else if ($duel_info['volgende_beurt'] == $_SESSION['naam']) {
            $pokemon_info = pokemon_data($duel_info['t_pokemonid']);
            $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);
            $opponent_info = pokemon_data($duel_info['u_pokemonid']);
            $opponent_info['naam_goed'] = pokemon_naam($opponent_info['naam'],$opponent_info['roepnaam']);
            $refresh = 1;
            $info1 = $pokemon_info['leven'];
            $info2 = $pokemon_info['levenmax'];
            $info3 = $pokemon_info['naam_goed'];
            $info4 = $pokemon_info['opzak_nummer'];
            $effect = $pokemon_info['effect'];
            $effect2 = $opponent_info['effect'];
            
            if ($pokemon_info['leven'] <= 0) {
              $mes = $opponent_info['naam_goed']." ".$txt['did']." ".$duel_info['laatste_aanval'].'. Seu Pokémon foi derrotado. <br>Você deve trocar de Pokémon!';
              $refresh = 4;
            }
            else{
              $mes = $opponent_info['naam_goed']." ".$txt['did']." ".$duel_info['laatste_aanval'].". ".$txt['your_turn'];
              $refresh = 1;
            }
          }
          else if ($duel_info['volgende_beurt'] == "end_screen") {
            $refresh = 5;
            $pokemon_info = pokemon_data($duel_info['t_pokemonid']);
            $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);
            $opponent_info = pokemon_data($duel_info['u_pokemonid']);
            $opponent_info['naam_goed'] = pokemon_naam($opponent_info['naam'],$opponent_info['roepnaam']);
            $info1 = $pokemon_info['leven'];
            $info2 = $pokemon_info['levenmax'];
            $effect2 = $opponent_info['effect'];
            $mes = $opponent_info['naam_goed']." ".$txt['did']." ".$duel_info['laatste_aanval'].'.<br> Você foi derrotado.';
          }
          else{
            $mes = "Error: 1101<br />Info: ".$duel_info['volgende_beurt']."/".$duel_info['id'];
          }
        }
      } 
    }
  }
  else{
    $mes = "Error: 6001";
  }
  
  
  $attack_info = atk($duel_info['laatste_aanval']);
  $attack_info2 = atk($duel_info['laatste_aanval2']);
  
  echo $refresh." | ".$mes." | ".$duel_info['laatste_aanval']." | ".$info1." | ".$info2." | ".$info3." | ".$info4." | ".$info5." | ".$info6." | ".$time_left." | ".$attack_info['soort']." | ".$attack_info2['is_zmoves']." | ".$duel_info['laatste_aanval2']." | ".$effect." | ".$effect2;
}
?>