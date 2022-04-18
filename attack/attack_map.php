<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Kijken of je wel pokemon bij je hebt
if ($gebruiker['in_hand'] == 0)
    header('location: index.php');


if (isset($_SESSION['battle_map'])) {
    header("Location: ./attack/attack_map");
    exit();
}


if (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='" . $_SESSION['id'] . "' AND `ei`='0' AND `opzak`='ja'")->num_rows > 0) {
    if ((isset($_POST[$_SESSION['attak_map_id']])) && (is_numeric($_POST[$_SESSION['attak_map_id']]))) {
        if (!isset($_POST['uid']) || !isset($_SESSION['map_uniqid']) || empty($_POST['uid']) || empty($_SESSION['map_uniqid']) || $_POST['uid'] != $_SESSION['map_uniqid']) {
            $ar = fopen('hack_exp_uid.log', 'a+');
            fwrite($ar, date("d-m-Y H:i:s") . ' - ' . $_SESSION['naam'] . ' ' . $_SESSION['id'] . "\n");
            fclose($ar);
            
            header("Location: ../error");
            exit("<script>location.href='../error'</script>");
        }
        unset($_SESSION['map_uniqid']);
        
        if ($_POST[$_SESSION['attak_map_id']] == 1)
            $gebied = 'Lavagrot';
        else if ($_POST[$_SESSION['attak_map_id']] == 2)
            $gebied = 'Vechtschool';
        else if ($_POST[$_SESSION['attak_map_id']] == 3)
            $gebied = 'Gras';
        else if ($_POST[$_SESSION['attak_map_id']] == 4)
            $gebied = 'Spookhuis';
        else if ($_POST[$_SESSION['attak_map_id']] == 5)
            $gebied = 'Grot';
        else if ($_POST[$_SESSION['attak_map_id']] == 6)
            $gebied = 'Water';
        else if ($_POST[$_SESSION['attak_map_id']] == 7)
            $gebied = 'Strand';
        
        
        if ($gebruiker['in_hand'] == 0)
            echo '<div class="blue"> ' . $txt['alert_no_pokemon'] . '</div>';
        else if ($gebied == 'Water' AND $gebruiker['Fishing rod'] == 0)
            $error = '<div class="red">' . $txt['alert_no_fishing_rod'] . '</div>';
        else if (($gebied == 'Grot' || $gebied == 'Lavagrot') AND $gebruiker['Cave suit'] == 0)
            $error = '<div class="red">' . $txt['alert_no_cave_soff'] . '</div>';
        else {
            //Zeldzaamheid bepalen
            // $zeldzaam = rand(1, 2500);

            // $zeldzaam = 2500;
            
            // if ($zeldzaam <= 100)
            //     $trainer = 1;
            // else if ($zeldzaam <= 2100)
            //     $zeldzaamheid = 1;
            // else if ($zeldzaam <= 2499)
            //     $zeldzaamheid = 2;
            // else if ($zeldzaam <= 2500) {
            //     $rand = rand(1, 500);
                
            //     if ($rand <= 10) {
            //         $zeldzaamheid = 3;
            //     } else {
            //         $zeldzaamheid = 1;
            //     }
            // }

            $zeldzaam = rand(1, 3050);
            
            if ($zeldzaam <= 50)
                $trainer = 1;
            else if ($zeldzaam <= 1950)
                $zeldzaamheid = 1;
            else if ($zeldzaam <= 3000)
                $zeldzaamheid = 2;
            else if ($zeldzaam <= 3020)
                $zeldzaamheid = 3;
            else if ($zeldzaam <= 3040)
                $zeldzaamheid = 4;
            else if ($zeldzaam <= 3045)
                $zeldzaamheid = 5;
            else if ($zeldzaam <= 3050)
                $zeldzaamheid = 8;

            if ($zeldzaamheid > 3 && $zeldzaamheid != 8) {
                $rand = rand(1, 200);
                
                if ($rand <= 30) {
                    $zeldzaamheid = array(4, 5, 8);
                    shuffle($zeldzaamheid);
                    $zeldzaamheid = $zeldzaamheid[0];
                } else {
                    $zeldzaamheid = rand(1, 2);
                }
            }
            
            if (!empty($evento_atual)){
              if ($evento_atual['name_id'] == 'pikachu_festival') {
                if ($zeldzaamheid == 8) {
                  $pikachu = find_pikachu();
                  if ($pikachu) {
                    $gebruiker['wereld'] = 'Todos';
                  }
                }
              }
            }
            
            $_SESSION['sec_key'] = mt_rand(100000, 999999);
            DB::exQuery("UPDATE `gebruikers` SET `sec_key`='{$_SESSION['sec_key']}' WHERE `user_id`={$_SESSION['id']} LIMIT 1");
            
            if ($trainer == 1) {
                $query = DB::exQuery("SELECT `naam` FROM `trainer` WHERE `badge`='' AND (`gebied`='" . $gebied . "' OR `gebied`='All') ORDER BY rand() limit 1")->fetch_assoc();
                include('attack/trainer/trainer-start.php');
                $pokemon_sql->data_seek(0);
                $opzak = $pokemon_sql->num_rows;
                $level = 0;
                while ($pokemon = $pokemon_sql->fetch_assoc())
                    $level += $pokemon['level'];
                $trainer_ave_level = $level / $opzak;
                //Make Fight
                $info              = create_new_trainer_attack($query['naam'], $trainer_ave_level, $gebied);
                DB::exQuery("UPDATE `gebruikers` SET `pagina`='trainer-attack' WHERE `user_id`='" . $_SESSION['id'] . "'");
                if (empty($info['bericht']))
                    header("Location: ../attack/trainer/trainer-attack");
                else
                    echo "<div class='red'>" . $txt['alert_no_pokemon'] . "</div>";
            } else {
                if (($gebruiker['rank'] > 15) && (!empty($gebruiker['lvl_choose']))) {
                    $level             = explode("-", $gebruiker['lvl_choose']);
                    $leveltegenstander = rand($level[0], $level[1]);
                } else
                    $leveltegenstander = rankpokemon($gebruiker['rank']);
                
                $query = DB::exQuery("SELECT wild_id FROM `pokemon_wild` WHERE `gebied`='" . $gebied . "' AND `wereld`='" . $gebruiker['wereld'] . "' AND `zeldzaamheid`='" . $zeldzaamheid . "' AND `aparece`='sim' ORDER BY rand() limit 1")->fetch_assoc();
                
                if (!empty($evento_atual)){
                  if ($evento_atual['name_id'] == 'pikachu_festival') {
                    if ($zeldzaamheid == 2 && $gebruiker['wereld'] == 'Kanto') {
                      if (rand(0, 5) == 1) {
                          $query = DB::exQuery("SELECT wild_id FROM `pokemon_wild` WHERE `wild_id`='25'")->fetch_assoc();
                      }
                    }
                  }
                }
                
                // if ($gebruiker['admin'] >= 3) {
                //     $query = DB::exQuery("SELECT wild_id FROM `pokemon_wild` WHERE `wild_id`='616'")->fetch_assoc();
                // }
                
                // if (empty($query['wild_id']) && $gebruiker['wereld'] == 'Kalos')
                //     $query = DB::exQuery("SELECT wild_id FROM `pokemon_wild` WHERE `gebied`='" . $gebied . "' AND `wereld`='Kalos' AND `zeldzaamheid`='1' AND `aparece`='sim' ORDER BY rand() limit 1")->fetch_assoc();
                
                // if (empty($query['wild_id']) && $gebruiker['wereld'] == 'Alola')
                //     $query = DB::exQuery("SELECT wild_id FROM `pokemon_wild` WHERE `gebied`='" . $gebied . "' AND `wereld`='Alola' AND `zeldzaamheid`='1' AND `aparece`='sim' ORDER BY rand() limit 1")->fetch_assoc();
                
                if (empty($query['wild_id']))
                    $query = DB::exQuery("SELECT wild_id FROM `pokemon_wild` WHERE `gebied`='" . $gebied . "' AND `wereld`='" . $gebruiker['wereld'] . "' AND `zeldzaamheid`='1' AND `aparece`='sim' ORDER BY rand() limit 1")->fetch_assoc();
                
                //echo "<div class='red'>".$txt['alert_error']." 100".$zeldzaamheid.".</div>";
                //else{
                if (!empty($query['wild_id'])) {
                    DB::exQuery("UPDATE `gebruikers` SET `voltaredirect`='attack_map' WHERE `user_id`='" . $_SESSION['id'] . "'");
                    if ($_POST[$_SESSION['attak_map_id']] == 3) {
                        $chance     = rand(1, 3);
                        $background = "gras-" . $chance;
                    } else if ($_POST[$_SESSION['attak_map_id']] == 6) {
                        $chance = rand(1, 2);
                        $background = "water-" . $chance;
                    }
                    
                    if ($season[1] == 2 || $season[1] == 4) {
                        $background .= '-'.$season[1];
                    }
                    
                    $_SESSION['background'] = $background;
                    
                    $pokesvivos = DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='" . $_SESSION['id'] . "' AND `opzak`='ja' AND `leven`>'0'")->num_rows;
                    if ($pokesvivos > 0) {
                        header("Location: ../attack/wild/wild-attack");
                        include("attack/wild/wild-start.php");
                        $info = create_new_attack($query['wild_id'], $leveltegenstander, $gebied);
                        DB::exQuery("UPDATE `gebruikers` SET `pagina`='attack',`background`='$background' WHERE `user_id`='" . $_SESSION['id'] . "'");
                    } else {
                        echo "<div class='red'>" . $txt['alert_no_pokemon'] . "</div>";
                    }
                }
                //}
            }
        }
    }
    
    $_SESSION['map_uniqid']   = uniqid($_SESSION['id']);
    $_SESSION['attak_map_id'] = uniqid('am');

    echo addNPCBox(11, 'Mapa de '.$gebruiker['wereld'], 'Olá, treinador! Seja bem vindo ao <b>MAPA</b> da região de '.$gebruiker['wereld'].'.<br>  
Busque sempre progredir no jogo, e para isso derrote e capture vários Pokémons. Lembre-se sempre de andar com Poke balls, pois nunca se sabe qual Pokémon você irá encontrar no seu caminho!');

    echo $error;
?>
  <div class="blue">Para poder ter acesso ao mar/lago compre a <a href="./market&shopitem=items">FISHING ROD</a> e para ter acesso à gruta adquira o <a href="./market&shopitem=items">CAVE SUIT</a> NO <a href="./market&shopitem=items">MERCADO</a>!</div>
  <style type="text/css">
  input {
    border: 0px;
    padding: 0px;
    margin: -2px;
    }
  </style>
<div class="box-content" style="padding: 10px">
<?php
    if ($gebruiker['wereld'] == "Kanto") {
        echo "<center>
    <table width='590' cellspacing='0' cellpadding='0'>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='236' height='228'><form method='post' name='Grot'><input type='image' onClick='Grot.submit();' src='" . $static_url . "/images/attackmap/kanto/grot.gif' alt='Gruta' /><input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='354' height='228'><form method='post' name='Gras'><input type='image' onClick='Gras.submit();' src='" . $static_url . "/images/attackmap/kanto/grasveld.gif' alt='Grama' /><input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='236' height='185'><form method='post' name='Lavagrot'><input type='image' onClick='Lavagrot.submit();' src='" . $static_url . "/images/attackmap/kanto/lavagrot.gif' alt='Lava' /><input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='354' height='185'><form method='post' name='Vechtschool'><input type='image' onClick='Vechtschool.submit();' src='" . $static_url . "/images/attackmap/kanto/vechtschool.gif'  alt='Dojô' /><input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='131'><form method='post' name='Strand'><input type='image' onClick='Strand.submit();' src='" . $static_url . "/images/attackmap/kanto/strand.gif' alt='Praia'/><input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='371' height='256'><form method='post' name='Water'><input type='image' onClick='Water.submit();' src='" . $static_url . "/images/attackmap/kanto/water.gif' alt='Água' /><input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='219' height='256'><form method='post' name='Spookhuis'><input type='image' onClick='Spookhuis.submit();' src='" . $static_url . "/images/attackmap/kanto/spookhuis.gif'  alt='Torre Assombrada' /><input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </center>";
    } else if ($gebruiker['wereld'] == "Johto") {
        echo "<center>
    <table width='590' cellspacing='0' cellpadding='0'>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='351' height='239'><form method='post' name='Vechtschool'><input type='image' onClick='Vechtschool.submit();' src='" . $static_url . "/images/attackmap/johto/vechtschool.gif' alt='Dojô' /><input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='239' height='239'><form method='post' name='Grot'><input type='image' onClick='Grot.submit();' src='" . $static_url . "/images/attackmap/johto/grot.gif' alt='Gruta' /><input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='348' height='255'><form method='post' name='Gras'><input type='image' onClick='Gras.submit();' src='" . $static_url . "/images/attackmap/johto/grasveld.gif' alt='Grama' /><input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='242' height='255'><form method='post' name='Lavagrot'><input type='image' onClick='Lavagrot.submit();' src='" . $static_url . "/images/attackmap/johto/lavagrot.gif' alt='Lava' /><input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='304' height='149'><form method='post' name='Spookhuis'><input type='image' onClick='Spookhuis.submit();' src='" . $static_url . "/images/attackmap/johto/spookhuis.gif' alt='Torre Assombrada' /><input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='286' height='149'><form method='post' name='Strand'><input type='image' onClick='Strand.submit();' src='" . $static_url . "/images/attackmap/johto/strand.gif' alt='Praia'/><input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='157'><form method='post' name='Water'><input type='image' onClick='Water.submit();' src='" . $static_url . "/images/attackmap/johto/water.gif' alt='Água' /><input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </center>";
    } else if ($gebruiker['wereld'] == "Hoenn") {
        echo "<center>
    <table width='590' cellspacing='0' cellpadding='0'>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='351' height='208'><form method='post' name='Lavagrot'><input type='image' onClick='Lavagrot.submit();' src='" . $static_url . "/images/attackmap/hoenn/lavagrot.gif' alt='Lava' /><input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='239' height='208'><form method='post' name='Spookhuis'><input type='image' onClick='Spookhuis.submit();' src='" . $static_url . "/images/attackmap/hoenn/spookhuis.gif' alt='Torre Assombrada' /><input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='267' height='129'><img src='" . $static_url . "/images/attackmap/hoenn/area_01.gif'></td>
            <td width='323' height='129'><form method='post' name='Gras'><input type='image' onClick='Gras.submit();' src='" . $static_url . "/images/attackmap/hoenn/grasveld_01.gif' alt='Grama' /><input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='137'><img src='" . $static_url . "/images/attackmap/hoenn/area_02.gif'></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='255' height='154'><form method='post' name='Vechtschool'><input type='image' onClick='Vechtschool.submit();' src='" . $static_url . "/images/attackmap/hoenn/vechtschool.gif' alt='Dojô' /><input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='335' height='154'><form method='post' name='Gras'><input type='image' onClick='Gras.submit();' src='" . $static_url . "/images/attackmap/hoenn/grasveld.gif' alt='Grama' /><input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
        </tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='127' height='172'><form method='post' name='Grot'><input type='image' onClick='Grot.submit();' src='" . $static_url . "/images/attackmap/hoenn/grot.gif' alt='Gruta' /><input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='297' height='172'><form method='post' name='Strand'><input type='image' onClick='Strand.submit();' src='" . $static_url . "/images/attackmap/hoenn/strand.gif' alt='Praia'/><input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='166' height='172'><form method='post' name='Water'><input type='image' onClick='Water.submit();' src='" . $static_url . "/images/attackmap/hoenn/water.gif' alt='Água' /><input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
        </table></td>
      </tr>
    </table>
    </center>";
    } else if ($gebruiker['wereld'] == "Sinnoh") {
        echo "<center>
    <table width='590' cellspacing='0' cellpadding='0'>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            
            <td width='192' height='112'><form method='post' name='Spookhuis'><input type='image' onClick='Spookhuis.submit();' src='" . $static_url . "/images/attackmap/sinnoh/spookhuis.gif' alt='Torre Assombrada' /><input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='257' height='112'><form method='post' name='Grot'><input type='image' onClick='Grot.submit();' src='" . $static_url . "/images/attackmap/sinnoh/grot.gif' alt='Gruta' /><input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='141' height='112'><form method='post' name='Water'><input type='image' onClick='Water.submit();' src='" . $static_url . "/images/attackmap/sinnoh/water.gif' alt='Água' /><input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='142'><form method='post' name='Water'><input type='image' onClick='Water.submit();' src='" . $static_url . "/images/attackmap/sinnoh/water2.gif' alt='Água' /><input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='379' height='264'><form method='post' name='Strand'><input type='image' onClick='Strand.submit();' src='" . $static_url . "/images/attackmap/sinnoh/strand.gif' alt='Praia'/><input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='211' height='264'><form method='post' name='Vechtschool'><input type='image' onClick='Vechtschool.submit();' src='" . $static_url . "/images/attackmap/sinnoh/vechtschool.gif' alt='Dojô' /><input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='385' height='273'><form method='post' name='Gras'><input type='image' onClick='Gras.submit();' src='" . $static_url . "/images/attackmap/sinnoh/grasveld.gif' alt='Grama' /><input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='205' height='273'><form method='post' name='Lavagrot'><input type='image' onClick='Lavagrot.submit();' src='" . $static_url . "/images/attackmap/sinnoh/lavagrot.gif' alt='Lava' /><input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </center>";
    } else if ($gebruiker['wereld'] == "Unova") {
        echo "<center>
    <table width='590' cellspacing='0' cellpadding='0'
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='126'><form method='post' name='Lavagrot'><input type='image' onClick='Lavagrot.submit();' src='" . $static_url . "/images/attackmap/unova/lavagrot.gif' alt='Lava' /><input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='145'><form method='post' name='Grot'><input type='image' onClick='Grot.submit();' src='" . $static_url . "/images/attackmap/unova/grot.gif' alt='Gruta' /><input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='160' height='211'><form method='post' name='Vechtschool'><input type='image' onClick='Vechtschool.submit();' src='" . $static_url . "/images/attackmap/unova/vechtschool.gif' alt='Dojô' /><input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='254' height='211'><form method='post' name='Gras'><input type='image' onClick='Gras.submit();' src='" . $static_url . "/images/attackmap/unova/grasveld.gif' alt='Grama' /><input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='176' height='211'><form method='post' name='Spookhuis'><input type='image' onClick='Spookhuis.submit();' src='" . $static_url . "/images/attackmap/unova/spookhuis.gif' alt='Torre Assombrada' /><input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='379' height='204'><form method='post' name='Strand'><input type='image' onClick='Strand.submit();' src='" . $static_url . "/images/attackmap/unova/strand.gif' alt='Praia'/><input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
            <td width='211' height='204'><img src='" . $static_url . "/images/attackmap/unova/port.gif' alt='Praia'/></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table width='590' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td width='590' height='114'><form method='post' name='Water'><input type='image' onClick='Water.submit();' src='" . $static_url . "/images/attackmap/unova/water.gif' alt='Água' /><input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'></form></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </center>";
    } else if ($gebruiker['wereld'] == "Kalos") {
        echo "<center>
    <img id='kalos' src='" . $static_url . "/images/attackmap/kalos/Kalos.gif' width='593' height='807' usemap='#kalos' />
<map name='kalos' id='kalos'>
<form method='post' name='Spookhuis'>
<input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='300,101,399,314' alt='Torre Assombrada'  onClick='Spookhuis.submit();' href='javascript:void(0)'  />
</form>
<form method='post' name='Gras'>
<input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='0,194,302,332' alt='Grama'   onClick='Gras.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='149,550,496,664' alt='Grama'   onClick='Gras.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='150,429,476,550' alt='Grama'   onClick='Gras.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='244,312,474,429' alt='Grama'   onClick='Gras.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='398,224,474,313' alt='Grama'   onClick='Gras.submit();' href='javascript:void(0)'     />
</form>
<form method='post' name='Lavagrot'>
<input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='0,0,593,104' alt='Lava' onClick='Lavagrot.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='395,99,593,225' alt='Lava' onClick='Lavagrot.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='0,0,302,217' alt='Lava' onClick='Lavagrot.submit();' href='javascript:void(0)'     />
</form>
<form method='post' name='Water'>
<input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='0,327,100,487' alt='Água'  onClick='Water.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='67,402,153,487' alt='Água'  onClick='Water.submit();' href='javascript:void(0)'     />
</form>
<form method='post' name='Grot'>
<input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='473,222,593,807' alt='Gruta'  onClick='Grot.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='0,661,593,807' alt='Gruta'  onClick='Grot.submit();' href='javascript:void(0)'     />
</form>
<form method='post' name='Vechtschool'>
<input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='1,485,150,662' alt='Dojô'  onClick='Vechtschool.submit();' href='javascript:void(0)'     />
</form>
<form method='post' name='Strand'>
<input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'><input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
<area shape='rect' coords='95,329,253,404' alt='Praia'  onClick='Strand.submit();' href='javascript:void(0)'     />
<area shape='rect' coords='152,346,244,430' alt='Praia'  onClick='Strand.submit();' href='javascript:void(0)'     />
</form>
</map>
</center>";
    } else if ($gebruiker['wereld'] == "Alola") {
        echo "<center>
                <div style='width: 590px; height: 798px;' class='attack_map'>
                    <div style='float: left; width: 209px; height: 227px;'>
                        <div style='float: left; width: 144px; height: 64px;'>
                            <form method='post' name='Gras'>
                                <input type='image' src='" . $static_url . "/images/attackmap/alola/Floresta.gif' alt='Grama' />
                                <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                                <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                            </form>
                        </div>
                        <div style='float: left; width: 65px; height: 64px;'>
                            <div style='float: left; width: 65px; height: 48px;'>
                                <form method='post' name='Gras'>
                                    <input type='image' src='" . $static_url . "/images/attackmap/alola/Grama2.gif' alt='Grama' />
                                    <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                                <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                                </form>
                            </div>
                            <div style='float: left; width: 65px; height: 16px;'>
                                <form method='post' name='Gras'>
                                    <input type='image' src='" . $static_url . "/images/attackmap/alola/Grama.gif' alt='Grama' />
                                    <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                                <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                                </form>
                            </div>
                        </div>
                        <div style='float: left; width: 209px; height: 163px;'>
                            <form method='post' name='Gras'>
                                <input type='image' src='" . $static_url . "/images/attackmap/alola/Grama2.gif' alt='Grama' />
                                <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                                <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                            </form>
                        </div>
                    </div>
                    <div style='float: left; width: 176px; height: 227px;'>
                        <div style='float: left; width: 176px; height: 160px;'>
                            <form method='post' name='Grot'>
                                <input type='image' src='" . $static_url . "/images/attackmap/alola/Gruta.gif' alt='Gruta' style='margin-top: 0px;'/>
                                <input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'>
                                <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                            </form>
                        </div>
                        <img style='float: left; width: 34px; height: 67px;' src='" . $static_url . "/images/attackmap/alola/Caminho.gif' alt='Road'/>
                        <img style='float: left; width: 126px; height: 67px;' src='" . $static_url . "/images/attackmap/alola/Caminho2.gif' alt='Road'/>
                        <div style='float: left; width: 16px; height: 67px;'>
                            <form method='post' name='Gras'>
                                <input type='image' src='" . $static_url . "/images/attackmap/alola/Floresta3.gif?n' alt='Grama' />
                                <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                                <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                            </form>
                        </div>
                    </div>
                    <div style='float: left; width: 205px; height: 160px;'>
                        <form method='post' name='Lavagrot'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Lava.gif' alt='Lava' />
                            <input type='hidden' value='1' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 205px; height: 67px;'>
                        <form method='post' name='Gras'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Floresta4.gif' alt='Grama' />
                            <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                             <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 243px; height: 238px;'>
                        <form method='post' name='Spookhuis'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Torre.gif' alt='Torre Fantasma' />
                            <input type='hidden' value='4' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 126px; height: 238px;'>
                        <form method='post' name='Vechtschool'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Dojo.gif' alt='Dojô' />
                            <input type='hidden' value='2' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 221px; height: 125px;'>
                        <form method='post' name='Gras'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Floresta5.gif' alt='Grama' />
                            <input type='hidden' value='3' name='" . $_SESSION['attak_map_id'] . "'>
                             <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <img style='float: left; width: 221px; height: 113px; margin-top: -2px;' src='" . $static_url . "/images/attackmap/alola/Caminho3.gif' alt='Road'/>
                    <div style='float: left; width: 590px; height: 45px;'>
                        <form method='post' name='Strand'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Praia.gif' alt='Praia'/>
                            <input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 332px; height: 45px;'>
                        <form method='post' name='Strand'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Praia2.gif' alt='Praia'/>
                            <input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 45px; height: 45px;'>
                        <form method='post' name='Grot'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Gruta2.gif' alt='Gruta' />
                            <input type='hidden' value='5' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 213px; height: 45px;'>
                        <form method='post' name='Strand'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Praia3.gif' alt='Praia'/>
                            <input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 590px; height: 36px;'>
                        <form method='post' name='Strand'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Praia4.gif' alt='Praia'/>
                            <input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 89px; height: 79px;'>
                        <form method='post' name='Water'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Agua.gif' alt='Água' />
                            <input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 132px; height: 79px;'>
                        <form method='post' name='Strand'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Praia5.gif' alt='Praia'/>
                            <input type='hidden' value='7' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 369px; height: 79px;'>
                        <form method='post' name='Water'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Agua2.gif' alt='Água' />
                            <input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                    <div style='float: left; width: 590px; height: 130px;'>
                        <form method='post' name='Water'>
                            <input type='image' src='" . $static_url . "/images/attackmap/alola/Agua3.gif' alt='Água' />
                            <input type='hidden' value='6' name='" . $_SESSION['attak_map_id'] . "'>
                            <input type='hidden' value='" . $_SESSION['map_uniqid'] . "' name='uid'>
                        </form>
                    </div>
                </div>
            </center><br><br>";
    }
} else {
    echo '<div style="padding-top:10px;"><div class="blue"><img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" /> ' . $txt['alert_no_pokemon'] . '</div></div>';
}
?>
</div>