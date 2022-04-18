<?php

if (isset($_POST['here'])) {
  //Check alive pokemon.
  if (DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' AND leven>'0'")->num_rows == 0)
    echo "Você não tem nenhum pokémon que possa batalhar!";
  else{
    //Include Duel Functions
    include_once('duel/duel-start.php');
    
    $excist_sql = DB::exQuery("SELECT id FROM duel WHERE uitdager='".$_SESSION['naam']."' OR tegenstander='".$_SESSION['naam']."'");
    if ($excist_sql->num_rows == 0) {
      if ($round_info['user_id_1'] == $_SESSION['id']) {
        $t = DB::exQuery("SELECT username, `character` FROM gebruikers WHERE user_id='".$round_info['user_id_2']."'")->fetch_assoc();
        $u['username'] = $gebruiker['username'];
        $u['character'] = $gebruiker['character'];
        $wat = 'uitdager';
        $starter = $u['username'];
      }
      else{
        $u = DB::exQuery("SELECT username, `character` FROM gebruikers WHERE user_id='".$round_info['user_id_1']."'")->fetch_assoc();
        $t['username'] = $gebruiker['username'];
        $t['character'] = $gebruiker['character'];
        $wat = 'tegenstander';
        $starter = $u['username'];
      }
      $date = date("Y-m-d H:i:s");
      $time = strtotime($date);
      DB::exQuery("INSERT INTO duel (datum, ronde_id, uitdager, tegenstander, u_character, t_character, status, laatste_beurt_tijd, laatste_beurt)
        VALUES ('".$date."', '".$round_info['ronde']."', '".$u['username']."', '".$t['username']."',  '".$u['character']."', '".$t['character']."',  'wait', '".$time."', '".$starter."')");
      $duel_id = DB::insertID();
      $_SESSION['duel']['duel_id'] = $duel_id;
      
      //Start Duel
      //Clear Player
      DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `user_id`='".$_SESSION['id']."'");
      //Update Player as Duel
      DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel' WHERE `user_id`='".$_SESSION['id']."'");
      //Copy Pokemon
      $count = 0;
      //Spelers van de pokemon laden die hij opzak heeft
      $pokemonopzaksql = DB::exQuery("SELECT * FROM pokemon_speler WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY opzak_nummer ASC");
      //Nieuwe stats berekenen aan de hand van karakter, en opslaan
      while($pokemonopzak = $pokemonopzaksql->fetch_assoc()) {
        //Alle gegevens opslaan, incl nieuwe stats
        DB::exQuery("INSERT INTO `pokemon_speler_gevecht` (`id`, `user_id`, `aanval_log_id`, `duel_id`, `levenmax`, `leven`, `exp`, `totalexp`, `effect`, `hoelang`) 
          VALUES ('".$pokemonopzak['id']."', '".$_SESSION['id']."', '-1', '".$excist['id']."', '".$pokemonopzak['levenmax']."', '".$pokemonopzak['leven']."', '".$pokemonopzak['exp']."', '".$pokemonopzak['totalexp']."', '".$pokemonopzak['effect']."', '".$pokemonopzak['hoelang']."')");  
      }
      ?>
        <span id="status">Aguarde</span>
        <script type="text/javascript">
        var t
        function status_check() {
          $.get("attack/tour_ready.php?duel_id="+<?php echo $duel_id; ?>+"&sid="+Math.random(), function(data) {
            if (data == 0) {
              $("#status").append(".")
              t = setTimeout('status_check()', 2000)
            } 
            else if (data == 1) {
              clearTimeout(t) 
              setTimeout("location.href='./attack/duel/duel-attack'", 0)
            }
            else if (data == 2) {
              clearTimeout(t) 
              $("#status").append("O oponente não respondeu. Você ganhou.")
            }
            else{
              $("#status").append("...")
              t = setTimeout('status_check()', 2000)
            }
          });
        }
        $("#status").html("Carregando")
        status_check()
        </script>
      <?php
    }
    else{
      $excist = $excist_sql->fetch_assoc();
      if ($_SESSION['id'] == $round_info['user_id_1']) $wat = 'u';
      else $wat = 't';
      $_SESSION['duel']['duel_id'] = $excist['id'];
      //Clear Player
      DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `user_id`='".$_SESSION['id']."'");
      //Update Player as Duel
      DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel' WHERE `user_id`='".$_SESSION['id']."'");
      //Copy Pokemon
      $count = 0;
      //Spelers van de pokemon laden die hij opzak heeft
      $pokemonopzaksql = DB::exQuery("SELECT * FROM pokemon_speler WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY opzak_nummer ASC");
      //Nieuwe stats berekenen aan de hand van karakter, en opslaan
      while($pokemonopzak = $pokemonopzaksql->fetch_assoc()) {
        //Alle gegevens opslaan, incl nieuwe stats
        DB::exQuery("INSERT INTO `pokemon_speler_gevecht` (`id`, `user_id`, `aanval_log_id`, `duel_id`, `levenmax`, `leven`, `exp`, `totalexp`, `effect`, `hoelang`) 
          VALUES ('".$pokemonopzak['id']."', '".$_SESSION['id']."', '-1', '".$excist['id']."', '".$pokemonopzak['levenmax']."', '".$pokemonopzak['leven']."', '".$pokemonopzak['exp']."', '".$pokemonopzak['totalexp']."', '".$pokemonopzak['effect']."', '".$pokemonopzak['hoelang']."')");
        if (($count == 0) AND ($pokemonopzak['leven'] > 0) AND ($pokemonopzak['ei'] == 0)) {
          $count++;
          DB::exQuery("UPDATE `duel` SET `".$wat."_pokemonid`='".$pokemonopzak['id']."', `".$wat."_used_id`=',".$pokemonopzak['id'].",' WHERE `id`='".$excist['id']."'");
        }  
      }
      
      $_SESSION['duel']['begin_zien'] = true;
      header("Location: ./attack/duel/duel-attack");
    }
  }
}
else{
  /*
  
  
  Daarna stappen doorlopen zoals dat met duel ook gebeurd
  */
  echo '
    <form method="post"><center>O Torneio começou.</center><br />
    <center>  <input type="submit" name="here" value="Batalhar!"></center>
    </form>';
}
?>