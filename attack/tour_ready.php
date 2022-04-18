<?php
if (isset($_GET['duel_id'])) { 
  //Connect With Database
  include_once("./app/includes/resources/config.php");
  //include duel functions
  include_once("duel/duel.inc.php");
  //Load Duel Data
  $duel_sql = DB::exQuery("SElECT `id`, `datum`, `uitdager`, `tegenstander`, `u_pokemonid`, `t_pokemonid`, `status`, `t_klaar`, `u_klaar` FROM `duel` WHERE `id`='".$_GET['duel_id']."'");
  if ($duel_sql->num_rows == 1) {
    $duel = $duel_sql->fetch_assoc();
    $time = strtotime(date("Y-m-d H:i:s"))-strtotime($duel['datum']);
    if (($duel['u_klaar'] == 1) OR ($duel['t_klaar'] == 1)) {
      $status = 1;
      $_SESSION['duel']['begin_zien'] = true;
  
      if ($duel['uitdager'] == $_SESSION['naam']) {
        $uitdager = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.id, ps.speed FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.user_id='".$_SESSION['id']."' AND psg.leven>'0' AND ps.ei='0' ORDER BY ps.opzak_nummer ASC LIMIT 1")->fetch_assoc();
        $tegenstander = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.speed FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.id='".$duel['t_pokemonid']."'")->fetch_assoc();
        $wat = 'u';
        $use = $uitdager['id'];
      }
      else{
        $tegenstander = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.id, ps.speed FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.user_id='".$_SESSION['id']."' AND psg.leven>'0' AND ps.ei='0' ORDER BY ps.opzak_nummer ASC LIMIT 1")->fetch_assoc();
        $uitdager = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.speed FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.id='".$duel['u_pokemonid']."'")->fetch_assoc();
        $wat = 't';
        $use = $tegenstander['id'];
      }
      //Check who is the fastest
      if ($uitdager['speed'] > $tegenstander['speed']) {
        $duel_info['laatste_beurt'] = $duel['uitdager']."_begin";
        $duel_info['volgende_beurt'] = $duel['uitdager'];
      }
      else{
        $duel_info['laatste_beurt'] = $duel['tegenstander']."_begin";
        $duel_info['volgende_beurt'] = $duel['tegenstander'];
      }

      //Save 
      DB::exQuery("UPDATE `duel` SET `".$wat."_pokemonid`='".$use."', `".$wat."_used_id`=',".$use.",', `laatste_beurt`='".$duel_info['laatste_beurt']."', `volgende_beurt`='".$duel_info['volgende_beurt']."' WHERE `id`='".$duel['id']."'");
      //Update Both pokedexes
     // DB::exQuery("UPDATE gebruikers SET `pok_gezien`=concat(pok_gezien,',".$uitdager['wild_id']."') WHERE user_id='".$tegenstander['user_id']."'");
     // DB::exQuery("UPDATE gebruikers SET `pok_gezien`=concat(pok_gezien,',".$tegenstander['wild_id']."') WHERE user_id='".$uitdager['user_id']."'");
      //
    }
    else if ($time > 120) {
      DB::exQuery("DELETE FROM `duel` WHERE `id`='".$_GET['duel_id']."'");
      //Remove Duel
      DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel_start' WHERE `user_id`='".$_SESSION['id']."'");
      DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `duel_id`='".$_GET['duel_id']."'");
      
      //Werkt niet.
      $tour = DB::exQuery("SELECT user_id_1, user_id_2, toernooi FROM toernooi_ronde WHERE id='".$duel_info['ronde_id']."' ORDER BY toernooi DESC")->fetch_assoc();
      if ($_SESSION['id'] == $tour['user_id_1']) {
        $you_id = '1';
        $other_id = '2';
      }
      else{
        $you_id = '2';
        $other_id = '1'; 
      }
      
      DB::exQuery("UPDATE toernooi_ronde SET dood_".$you_id."='0', dood_".$other_id."='3', winnaar_id='".$_SESSION['id']."' WHERE id='".$duel_info['ronde_id']."' AND toernooi='".$tour['toernooi']."' AND gereed<'3'");
      DB::exQuery("UPDATE toernooi_ronde SET user_id_".$you_id."='".$_SESSION['id']."', gereed=gereed+'1' WHERE user_id_".$you_id."='-".$duel_info['ronde_id']."' AND toernooi='".$tour['toernooi']."' AND gereed<'3'");
      $status = 2;
    }
    else $status = 0;
  }
  else $status = 2;
  echo $status;
}
?>