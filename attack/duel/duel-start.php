<?php
function start_duel($duel_id,$wat) {
  //Clear Player
  DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `user_id`='".$_SESSION['id']."'");
  //Update Player as Duel
  DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel' WHERE `user_id`='".$_SESSION['id']."'");
  
  $pokemonopzaksqlx = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY opzak_nummer ASC");
  
  while($pokemonopzakx = $pokemonopzaksqlx->fetch_assoc()) {
  DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `id`='".$pokemonopzakx['id']."'"); 
  }
  
  //Copy Pokemon
  return create_player($duel_id,$wat);
}


function create_player($duel_id,$wat) {
  $count = 0;
  //Spelers van de pokemon laden die hij opzak heeft
  $pokemonopzaksql = DB::exQuery("SELECT * FROM pokemon_speler WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY opzak_nummer ASC");
  //Nieuwe stats berekenen aan de hand van karakter, en opslaan
  while($pokemonopzak = $pokemonopzaksql->fetch_assoc()) {
    //Alle gegevens opslaan, incl nieuwe stats
    DB::exQuery("INSERT INTO `pokemon_speler_gevecht` (`id`, `user_id`, `aanval_log_id`, `duel_id`, `levenmax`, `leven`, `attack`, `defence`, `speed`, `spc.attack`, `spc.defence`, `exp`, `totalexp`, `effect`, `hoelang`) VALUES ('".$pokemonopzak['id']."', '".$_SESSION['id']."', '-1', '".$duel_id."', '".$pokemonopzak['levenmax']."', '".$pokemonopzak['leven']."', '".$pokemonopzak['attack']."', '".$pokemonopzak['defence']."', '".$pokemonopzak['speed']."', '".$pokemonopzak['spc.attack']."', '".$pokemonopzak['spc.defence']."', '".$pokemonopzak['exp']."', '".$pokemonopzak['totalexp']."', '".$pokemonopzak['effect']."', '".$pokemonopzak['hoelang']."')");
    if (($count == 0) AND ($wat == 'tegenstander') AND ($pokemonopzak['leven'] > 0) AND ($pokemonopzak['ei'] == 0)) {
      $count++;
      DB::exQuery("UPDATE `duel` SET `t_pokemonid`='".$pokemonopzak['id']."', `t_used_id`=',".$pokemonopzak['id'].",' WHERE `id`='".$duel_id."'");
    }  
  }
}
?>