<?php
$error = "<center><div style='padding-bottom:10px;'>Escolha o pokémon no qual você vai usar ".$_GET['name'].".</div></center>"; 
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
if ($gebruiker_item[$_GET['name']] <= 0) {
	?>
  <script>  
  	parent.$.colorbox.close();
  </script>
  <?php
  exit;
}

$button = true;

//Afbreken
if (isset($_POST['nee'])) {
  ?>
  <script>  
  	parent.$.colorbox.close();
  </script>
  <?php
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<title><?=$site_title;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css?<?=rand(1111, 9999);?>" />
</head>
  
  <body>
  <?php
//Als een pokemon moet evolueren met de steen
if (isset($_POST['zeker'])) {
  //Gegevens laden van de des betreffende pokemon
  $pokemon = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.*, karakters.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id INNER JOIN karakters ON pokemon_speler.karakter = karakters.karakter_naam WHERE pokemon_speler.id='".$_POST['pokemonid']."'")->fetch_assoc();
  //Gegevens halen uit de levelen tabel
  $levelensql = DB::exQuery("SELECT nieuw_id FROM `levelen` WHERE `id`='".$_POST['levelenid']."'");
  $levelen = $levelensql->fetch_assoc();
  if (empty($_POST['pokemonid']))
    $error = 'FOUT 2!<br /> Code: '.$_POST['pokemonid'].'<br />Como tudo sobre a';
  else if ($levelensql->num_rows == 1) {
    if ($pokemon['wild_id'] == '25' && $_SESSION['region'] == 'Alola') {
        $levelen['nieuw_id'] = '26001';
    } else if ($pokemon['wild_id'] == '102' && $_SESSION['region'] == 'Alola') {
        $levelen['nieuw_id'] = '103001';
    }
    
    $update = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='".$levelen['nieuw_id']."'")->fetch_assoc();
	
    $attackstat   = round((((($pokemon['attack_iv'] + 2 * $update['attack_base'] + floor($pokemon['attack_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['attack_up']) * $pokemon['attack_add']);
    $defencestat  = round((((($pokemon['defence_iv'] + 2 * $update['defence_base'] + floor($pokemon['defence_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['defence_up']) * $pokemon['defence_add']) ;
    $speedstat    = round((((($pokemon['speed_iv'] + 2 * $update['speed_base'] + floor($pokemon['speed_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['speed_up']) * $pokemon['speed_add']);
    $spcattackstat  = round((((($pokemon['spc.attack_iv'] + 2 * $update['spc.attack_base'] + floor($pokemon['spc.attack_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['spc_up']) * $pokemon['spc.attack_add']);
    $spcdefencestat = round((((($pokemon['spc.defence_iv'] + 2 * $update['spc.defence_base'] + floor($pokemon['spc.defence_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['spc_up']) * $pokemon['spc.defence_add']);
    $hpstat     = round(((($pokemon['hp_iv'] + 2 * $update['hp_base'] + floor($pokemon['hp_ev'] / 4)) * $pokemon['level'] / 100) + 10 + $pokemon['level']) + $pokemon['hp_up']);
    $ability = explode(',', $update['ability']);
    $rand_ab = rand(0, (sizeof($ability) - 1));
  	$ability = $ability[$rand_ab];
    //Pokemon gegevens en nieuwe Stats opslaan
    DB::exQuery("UPDATE `pokemon_speler` SET `wild_id`='".$levelen['nieuw_id']."', `attack`='".$attackstat."', `defence`='".$defencestat."', `speed`='".$speedstat."', `spc.attack`='".$spcattackstat."', `spc.defence`='".$spcdefencestat."', `levenmax`='".$hpstat."', `leven`='".$hpstat."', `ability`='".$ability."' WHERE `id`='".$pokemon['id']."'");
    //Pokemon opslaan als in bezit
    update_pokedex($update['wild_id'],$pokemon['wild_id'],'evo');
    //Stone weg
    DB::exQuery("UPDATE `gebruikers_item` SET `".$_POST['item']."`=`".$_POST['item']."`-'1' WHERE `user_id`='".$_SESSION['id']."'");
    //Post leeg maken.
    unset($_POST['zeker']);
    
    $error = '<div class="green"><img src="' . $static_url . '/images/icons/green.png"> Parabéns, seu  <strong>'.$pokemon['naam'].'</strong> evoluiu para <strong>'.$update['naam'].'</strong> com '.$_POST['item'].'!</div>';
     
  }
  else{
    $error = 'FOUT 1!<br /> Code: '.$_POST['levelenid'].'<br />Como tudo sobre a';
  }
  ?>
  
<script type="text/javascript">
	var num = parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html().replace('x', '').replace('<b>', '').replace('</b>', '');
	if ((num - 1) > 0)	parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html('<b>'+(num - 1)+'x</b>');
	else {
		parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').empty().parent().remove();
		parent.$.colorbox.close();
	}
</script>
  <script src="<?=$static_url?>/javascripts/jquery-2.1.3.min.js"></script>
  <script src="<?=$static_url;?>/javascripts/poke.evolve.js"></script>
  <?php
      $sprite_1 = $pokemon['shiny'] == 1 ? 'shiny' : 'pokemon'.'/'.$pokemon['wild_id'].'.gif';
      $sprite_2 = $pokemon['shiny'] == 1 ? 'shiny' : 'pokemon'.'/'.$update['wild_id'].'.gif';
  ?>
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="3"><?php if ($error) echo $error; else echo "&nbsp"; ?></th></tr>
			<tr>
        <th style="width: 100%">
            <center><img src="<?=$static_url?>/images/<?=$sprite_1;?>" class="pokemon" id="evolution"/></center>
        </th>
			</tr>
		</thead>
  </table>
  </div>

  <script>$('#evolution').wlEvolve('<?=$static_url?>'+'/images/'+'<?=$sprite_1?>', '<?=$static_url?>'+'/images/'+'<?=$sprite_2?>')</script>
	
  <?php
}
else{
	if (isset($_POST['evolve'])) {
  	list ($pokemonid, $levelenid, $pokemonnaam, $wildid) = explode('/', $_POST['pokemonid']);
  	if (empty($pokemonid))
		  echo '<div class="red">Escolha um pokémon para usar '.$_POST['item'].'.';
		else if (empty($levelenid))
      echo '<div class="red">FOUT!<br /> Code: '.$_POST['pokemonid'].'<br />';
  	else if (DB::exQuery("SELECT `id` FROM `levelen` WHERE `wild_id`='".$wildid."' AND `wat`='evo' AND `stone`='".$_POST['item']."'")->num_rows == 0)
		  echo '<div class="red">O pokémon '.$pokemonnaam.' não pode ter '.$_POST['item'].' usada nele.';  
  	else if (DB::exQuery("SELECT `id` FROM `levelen` WHERE `id`='".$levelenid."'")->num_rows == 0)
		  echo '<div class="red">Fout!<br /> Code: '.$_POST['pokemonid'].'<br>';  
   	else{
			echo '<center><div class="blue">Tem certeza que deseja evoluir '.$pokemonnaam.'?<br />';
			echo '<form method="post">
        <input type="hidden" Value="'.$_POST['item'].'" name="item">
        <input type="hidden" Value="'.$pokemonid.'" name="pokemonid">
        <input type="hidden" Value="'.$levelenid.'" name="levelenid">   
				<input type="submit" Value="Sim" name="zeker" class="button"> | <input type="submit" Value="Não" name="nee" class="button">
				</form></div></center>';
    }
  }
  else{
  ?>

    <form method="post" name="useitem">
	
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="10"><?php if ($error) echo $error; else echo "&nbsp"; ?></th></tr>
			<tr>
    		<th width="50"><center><strong>#</strong></center></th>
    		<th width="100"><strong>Pokémon:</strong></th>
    		<th width="150"><strong>Nome:</strong></th>
    		<th width="100" align="center"><strong>Nível:</strong></th>
    		<th width="100" align="center"><strong>Evolui:</strong></th>
			</tr>
		</thead>
		<tbody>
		
    <?php
    //Pokemon laden van de gebruiker die hij opzak heeft
    $poke = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
    
    //Pokemons die hij opzak heeft weergeven  
    for($teller=0; $pokemon = $poke->fetch_assoc(); $teller++) {
      if ($pokemon['ei'] != 1) {
        $kan = "<img src='".$static_url."/images/icons/red.png' alt='Não usável'>";
        $disabled = 'disabled';   
        //Als er een result is kan pokemon evolueren.
        $stoneevolvesql = DB::exQuery("SELECT `id`, `stone`, `nieuw_id` FROM `levelen` WHERE `wild_id`='".$pokemon['wild_id']."' AND `stone`='".$_GET['name']."'");
        $stoneevolve = $stoneevolvesql->fetch_assoc();
        
        //Heeft de stone werking?
        if ($stoneevolvesql->num_rows >= 1) {
        	$kan = "<img src='".$static_url."/images/icons/green.png' alt='Usável'>";
        	$disabled = '';
        }
                echo '<tr>';
        //Als pokemon geen baby is
        if ($pokemon['ei'] != 1) {
          echo '
            <td><center><input type="hidden" name="levelenid" value="'.$stoneevolve['id'].'">
            <input type="radio" name="pokemonid" value="'.$pokemon['id'].'/'.$stoneevolve['id'].'/'.$pokemon['naam'].'/'.$pokemon['wild_id'].'" '.$disabled.'/>
            <input type="hidden" name="pokemonnaam" value="'.$pokemon['naam'].'"></center></td>
          ';             
        }
        else
          echo '<td><center><input type="radio" id="niet'.$i.'" name="niet" disabled/></center></td></td>';
        
        $pokemon = pokemonei($pokemon, $txt);
        $pokemon['naam_goed'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);
        
        echo '
          <td><center><img src="'.$static_url.'/'.$pokemon['animatie'].'" width="32" height="32"></center></td>
          <td>'.$pokemon['naam_goed'].'</td>
          <td>'.$pokemon['level'].'</td>
        ';
        
        //Als pokemon geen baby is
        if ($pokemon['ei'] != 1) echo '<td>'.$kan.'</td>';
        else echo '<td>Error</td>';
        	
        echo '</tr>';
      }
    }
    
 
      ?>
	  
	  </tbody>
		<tfoot>
		<?php    if ($button) { ?>
		<tr>
			<td colspan="5" align="right">
				<input type="hidden" name="item" value="<?=$_GET['name'];?>" />
				<input type="submit" name="evolve" value="Ok!" class="button" />
			</td>
		</tr>
		    <?php } ?>
		
		</tfoot>
	</table></div>
</form>


    <?php
    }
  }
?>
</body></html>