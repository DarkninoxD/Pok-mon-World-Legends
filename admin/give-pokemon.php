<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }

if (isset($_POST['geef'])) {  

	//Gegevens laden van speler
	//$aantal = DB::exQuery("SELECT `user_id` FROM `pokemon_speler` WHERE `user_id`='".$_POST['user_id']."' AND `opzak`='ja'")->num_rows;

	if ($_POST['user_id'] == '') echo '<div class="red"> Digite o ID de algum treinador.</div>';
	else if ($_POST['wild_id'] == 'none') echo '<div class="red"> Selecione algum pokémon.</div>';
	//else if ($aantal == 6) echo '<div class="red"> O treinador já tem 6 pokémons com ele.</div>';
	else if (DB::exQuery("SELECT user_id FROM gebruikers WHERE user_id='".$_POST['user_id']."'")->num_rows == 0) echo '<div class="red"> O treinador não existe.</div>';
	else if (($_POST['level'] > 100) OR ($_POST['level'] < 5)) echo '<div class="red"> Level muito baixo ou muito alto.</div>';
	else{
    //Load pokemon basis
    $new_computer_sql = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='".$_POST['wild_id']."'")->fetch_assoc();

    //Alle gegevens vast stellen voordat alles begint.
    $new_computer['id']             = $new_computer_sql['wild_id'];
    $new_computer['pokemon']        = $new_computer_sql['naam'];
    $new_computer['aanval1']        = $new_computer_sql['aanval_1'];
    $new_computer['aanval2']        = $new_computer_sql['aanval_2'];
    $new_computer['aanval3']        = $new_computer_sql['aanval_3'];
    $new_computer['aanval4']        = $new_computer_sql['aanval_4'];
    $ability        = explode(',', $new_computer_sql['ability']);
    $klaar          = false;
    $loop           = 0;
    $lastid         = 0;
	
    //Loop beginnen
    do{ 
      $teller = 0;
      $loop++;
      //Levelen gegevens laden van de pokemon
      $levelenquery = DB::exQuery("SELECT * FROM `levelen` WHERE `wild_id`='".$new_computer['id']."' AND `level`<='".$_POST['level']."' ORDER BY `id` ASC ");

      //Voor elke pokemon alle gegeven behandelen
      while($groei = $levelenquery->fetch_assoc()) {

        //Teller met 1 verhogen
        $teller++;
        //Is het nog binnen de level?
        if ($_POST['level'] >= $groei['level']) {
          //Is het een aanval?
          if ($groei['wat'] == 'att') {
            //Is er een plek vrij
            if (empty($new_computer['aanval1'])) $new_computer['aanval1'] = $groei['aanval'];
            else if (empty($new_computer['aanval2'])) $new_computer['aanval2'] = $groei['aanval'];
            else if (empty($new_computer['aanval3'])) $new_computer['aanval3'] = $groei['aanval'];
            else if (empty($new_computer['aanval4'])) $new_computer['aanval4'] = $groei['aanval'];
            //Er is geen ruimte, dan willekeurig een aanval kiezen en plaatsen
            else{
              if (($new_computer['aanval1'] != $groei['aanval']) AND ($new_computer['aanval2'] != $groei['aanval']) AND ($new_computer['aanval3'] != $groei['aanval']) AND ($new_computer['aanval4'] != $groei['aanval'])) {
                $nummer = rand(1,4);
                if ($nummer == 1) $new_computer['aanval1'] = $groei['aanval'];
                else if ($nummer == 2) $new_computer['aanval2'] = $groei['aanval'];
                else if ($nummer == 3) $new_computer['aanval3'] = $groei['aanval'];
                else if ($nummer == 4) $new_computer['aanval4'] = $groei['aanval'];
              }
            }
          }

          //Evolueert de pokemon
          else if ($groei['wat'] == "evo") {
            $evo = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='".$groei['nieuw_id']."'")->fetch_assoc();
            $new_computer['id']             = $groei['nieuw_id'];
            $new_computer['pokemon']        = $groei['naam'];
            $loop = 0;
            break;
          }
        }

        //Er gebeurd niks dan stoppen
        else{
          $klaar = true;
          break;
        }
      }
      if ($teller == 0) {
        break;
        $klaar = true;
      }
      if ($loop == 2) {
        break;
        $klaar = true;
      }
    }
	while(!$klaar);

    //Karakter kiezen 
    $karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY rand() limit 1")->fetch_assoc();

    //Expnodig opzoeken en opslaan
    $level = $_POST['level']+1;
    $experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='".$new_computer_sql['groei']."' AND `level`='".$level."'")->fetch_assoc();

      $attack_iv = rand(min($_POST['min'], $_POST['max']), max($_POST['min'], $_POST['max']));
      $defence_iv = rand(min($_POST['min'], $_POST['max']), max($_POST['min'], $_POST['max']));
      $speed_iv = rand(min($_POST['min'], $_POST['max']), max($_POST['min'], $_POST['max']));
      $spcattack_iv = rand(min($_POST['min'], $_POST['max']), max($_POST['min'], $_POST['max']));
      $spcdefence_iv = rand(min($_POST['min'], $_POST['max']), max($_POST['min'], $_POST['max']));
      $hp_iv = rand(min($_POST['min'], $_POST['max']), max($_POST['min'], $_POST['max']));

    
    //Promo IV minimo 15

    //Stats berekenen
    $new_computer['attackstat']     = round(((($new_computer_sql['attack_base']*2+$attack_iv)*$_POST['level']/100)+5)*$karakter['attack_add']);
    $new_computer['defencestat']    = round(((($new_computer_sql['defence_base']*2+$defence_iv)*$_POST['level']/100)+5)*$karakter['defence_add']);
    $new_computer['speedstat']      = round(((($new_computer_sql['speed_base']*2+$speed_iv)*$_POST['level']/100)+5)*$karakter['speed_add']);
    $new_computer['spcattackstat']  = round(((($new_computer_sql['spc.attack_base']*2+$spcattack_iv)*$_POST['level']/100)+5)*$karakter['spc.attack_add']);
    $new_computer['spcdefencestat'] = round(((($new_computer_sql['spc.defence_base']*2+$spcdefence_iv)*$_POST['level']/100)+5)*$karakter['spc.defence_add']);
    if ($new_computer_sql['wild_id'] != 292) {
      $new_computer['hpstat']       = round(((($new_computer_sql['hp_base']*2+$hp_iv)*$_POST['level']/100)+$_POST['level'])+10);
    } else {
      $new_computer['hpstat']       = 1;
    }
    

    // $opzak = $aantal+1;

    //Save Computer
    $tijd = '0000-00-00 00:00:00';
    if ($_POST['egg'] == 'n') {
      $egg = 0;
    } else {
      $tijd = date('Y-m-d H:i:s');
      $egg = 1;
    }

    $date = date('Y-m-d H:i:s');

    $rand_ab = rand(0, (sizeof($ability) - 1));
    $ability = $ability[$rand_ab];
    
    DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`, `user_id`, `opzak`, `opzak_nummer`, `karakter`, `level`, `levenmax`, `leven`, `totalexp`, `expnodig`, `attack`, `defence`, `speed`, `spc.attack`, `spc.defence`, `attack_iv`, `defence_iv`, `speed_iv`, `spc.attack_iv`, `spc.defence_iv`, `hp_iv`, `attack_ev`, `defence_ev`, `speed_ev`, `spc.attack_ev`, `spc.defence_ev`, `hp_ev`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `effect`, `ei`, `ei_tijd`, `ability`, `capture_date`) 
    VALUES ('".$new_computer['id']."', '".$_POST['user_id']."', 'nee', '', '".$karakter['karakter_naam']."', '".$_POST['level']."', '".$new_computer['hpstat'] ."', '".$new_computer['hpstat'] ."', '".$experience['punten']."', '".$experience['punten']."', '".$new_computer['attackstat']."', '".$new_computer['defencestat']."', '".$new_computer['speedstat']."', '".$new_computer['spcattackstat']."', '".$new_computer['spcdefencestat']."', '".$attack_iv."', '".$defence_iv."', '".$speed_iv."', '".$spcattack_iv."', '".$spcdefence_iv."', '".$hp_iv."', '".$new_computer_sql['effort_attack']."', '".$new_computer_sql['effort_defence']."', '".$new_computer_sql['effort_spc.attack']."', '".$new_computer_sql['effort_spc.defence']."', '".$new_computer_sql['effort_speed']."', '".$new_computer_sql['effort_hp']."', '".$new_computer['aanval1']."', '".$new_computer['aanval2']."', '".$new_computer['aanval3']."', '".$new_computer['aanval4']."', '".$new_computer_sql['effect']."', '".$egg."', '".$tijd."', '".$ability."', '".$date."')");
	
	
	$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a> transferiu um pokémon para sua casa.';
				DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen)
	VALUES (NULL, NOW(), '".$_POST['user_id']."', '".$event."', '0')");
	
	
	echo '<div class="green"> Pokémon entregue na casa com sucesso.</div>';

  }
}
?>

<form method="post" id="form">
<center>
<p>De pokémons, o level minimo do pokémon para ser enviado é 5.<br>O Pokémon é enviado para a casa do trainer.</p>
<table width="600">
	<tr>
    	<td width="150">ID do treinador:</td>
        <td width="200"><input type="text" name="user_id" class="text_long" value="<?php if ($_GET['player'] != '') echo $_GET['player']; ?>"></td>
    </tr>
    <tr>
    	<td>Pokémon:</td>
        <td><select id="poke" name="wild_id" class="text_select">
				<option value="none">Escolha</option>
				<?php 
                  $allpokemonsql = DB::exQuery("SELECT wild_id, naam, type1 FROM pokemon_wild ORDER BY naam ASC");
                  while($allpokemon = $allpokemonsql->fetch_assoc()) {
                    $allpokemon['naam_goed'] = computer_naam($allpokemon['naam']);
                      echo '<option value="'.$allpokemon['wild_id'].'">'.$allpokemon['naam_goed'].' - '.$allpokemon['type1'].'</option>';
                  }
                ?>
			</select><button class="btn" type="button" onclick="randomizer();">Randomizer</button></td>
    </tr>
    <tr>
    	<td>Level:</td>
        <td><input type="number" name="level" class="text_long" min="5" max="100" value="100"/></td>
    </tr>
    <tr>
    	  <td>IVS:</td>
        <td><input type="number" name="min" max="31" min="0" value="0">IV's Min <br><input type="number" name="max" max="31" min="0" value="31">IV's Max</td>
    </tr>
    <tr>
      <td>EGG:</td>
      <td><input type="radio" name="egg" value="n" selected="selected"> Sem egg || <input type="radio" name="egg" value="y"> Com egg</td>
    </tr>
    <tr>
                  <td></td>
        <td><input type="submit" name="geef" value="Dar!" class="button" /></td>
    </tr>
</table>

<script>
  function randomizer () {
    var select = document.getElementById('poke');
    var items = select.getElementsByTagName('option');
    var index = Math.floor(Math.random() * items.length);
    select.selectedIndex = index;
  }


</script>
</center>
</form>