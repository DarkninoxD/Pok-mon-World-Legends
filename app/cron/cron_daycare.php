<?php
require_once('../includes/resources/config.php');

$daycare_sql = DB::exQuery("SELECT pokemonid, level, levelup FROM daycare WHERE ei='0' AND levelup<'15' ORDER BY id");
while ($daycare = $daycare_sql->fetch_assoc()) {
  $leveltotal = $daycare['level'] + $daycare['levelup'];
  if ($leveltotal < 100) {
    $levelup = mt_rand(0, 1);
    #Pokemon lvlup updaten naar +0 of +1
    DB::exQuery("UPDATE daycare SET levelup=levelup+'" . $levelup . "' WHERE pokemonid='" . $daycare['pokemonid'] . "'");
  }
}

#-------------------------------- EI CHECK ------------------------------------#
$sql = DB::exQuery("SELECT user_id, COUNT( user_id ) AS owner FROM daycare GROUP BY user_id");
while ($daycare = $sql->fetch_assoc()) {
  $random = mt_rand(1, 4);
  $usr = DB::exQuery("SELECT premiumaccount FROM gebruikers WHERE user_id='".$daycare['user_id']."'")->fetch_assoc();
  if ($usr['premiumaccount'] < time()) {
DB::exQuery("INSERT INTO gebeurtenis (datum, ontvanger_id, bericht, gelezen) VALUES (NOW(), '".$daycare['user_id']."', 'Você não é Premium! Seu ovo foi perdido no jardim de infância.', '0')");
DB::exQuery("DELETE FROM `daycare` WHERE `user_id`=".$daycare['user_id']." AND `ei`='1'");        
  } else {
  if ($random == 1) {
    if ($daycare['owner'] == 2) {
      $daycare_sql = DB::exQuery("SELECT pokemonid, user_id, naam FROM daycare WHERE `user_id`='" . $daycare['user_id'] . "' ORDER BY id");
      for ($i = 1; $daycare = $daycare_sql->fetch_assoc(); $i++) {
        if ($i == 1) {
          $pokemon1  = $daycare['naam'];
          $shinysql1 = DB::exQuery("SELECT shiny FROM pokemon_speler WHERE id='" . $daycare['pokemonid'] . "'")->fetch_assoc();
        } else if ($i == 2) {
          $pokemon2  = $daycare['naam'];
          $shinysql2 = DB::exQuery("SELECT shiny FROM pokemon_speler WHERE id='" . $daycare['pokemonid'] . "'")->fetch_assoc();
        }
        $user_id = $daycare['user_id'];
      }
      
      #Kijken of beide pokemon shiny zijn
      if ($shinysql1['shiny'] == 1 && $shinysql2['shiny'] == 1)
        $shiny = 1;
      else
        $shiny = 0;
      if (($pokemon1 == $pokemon2) OR ($pokemon1 == 'Ditto') OR ($pokemon2 == 'Ditto')) {
        if ($pokemon1 == "Ditto")
          $pokemon = $pokemon2;
        else
          $pokemon = $pokemon1;
        #Check if pokemon is not rare
        $rare = DB::exQuery("SELECT `wild_id`, `zeldzaamheid` FROM `pokemon_wild` WHERE `naam`='" . $pokemon . "'")->fetch_assoc();
        if ($rare['zeldzaamheid'] != 3) {
        if ($rare['type1'] != "Shadow") {
          $wildid = $rare['wild_id'];
          while (1) {
            $level_sql = DB::exQuery("SELECT `wild_id` FROM `levelen` WHERE `nieuw_id`='" . $wildid . "' AND `wat`='evo'");
            if ($level_sql->num_rows == 0)
              break;
            else {
              $select = $level_sql->fetch_assoc();
              if ($wildid != $select['wild_id'])
                $wildid = $select['wild_id'];
            }
          }
          $name = DB::exQuery("SELECT `naam` FROM pokemon_wild WHERE wild_id='" . $wildid . "'")->fetch_assoc();
          #Eitje in daycare zetten
          DB::exQuery("INSERT INTO daycare SET level='5',levelup='" . $shiny . "',user_id='" . $user_id . "',naam='" . $name['naam'] . "',ei='1'");
          }
        }
      }
    }
  }
  }
}
#-------------------------------- / EI CHECK ------------------------------------#

#Tijd opslaan van wanneer deze file is uitevoerd
$tijd = date("Y-m-d H:i:s");
DB::exQuery("UPDATE `cron` SET `tijd`='" . $tijd . "' WHERE `soort`='daycare'");

  echo "Cron executado com sucesso.";
?>