<?php
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_tmhm` WHERE `user_id`=" . $_SESSION['id'] . " LIMIT 1")->fetch_assoc();
if ($gebruiker_item[$_GET['name']] <= 0)	echo '<script>parent.$.colorbox.close();</script>';

#Als er een result is kan pokemon evolueren.
if (isset($_POST['kies']) && is_numeric($_POST['pokemonid'])) {
	if (empty($_POST['pokemonid']))	$message =  '<div class="red">Você deve escolher um pokémon!</div>';
	else	exit(header('Location: ./ajax.php?act=use_attack_finish&name=' . $_GET['name'] . '&pokemonid=' . $_POST['pokemonid']));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<title><?=$site_title;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css" />
</head>
<body>
<?php	
if (!empty($message))	echo $message;

$check = DB::exQuery("SELECT `type1`,`type2` FROM `tmhm` WHERE `naam`='" . $_GET['name'] . "' LIMIT 1")->fetch_assoc();
$poke = DB::exQuery("SELECT `pokemon_wild`.`wild_id`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2`,`pokemon_wild`.`naam`,`pokemon_speler`.`id`,`pokemon_speler`.`level`,`pokemon_speler`.`shiny`,`pokemon_speler`.`user_id`,`pokemon_speler`.`ei` FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `user_id`=" . $_SESSION['id'] . " AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
?>
<form method="post">
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="10">Escolha o pokémon para qual você vai usar <?=$_GET['name'];?>.</th></tr>
			<tr>
				<th width="20">#</th>
				<th width="200" colspan="2">Pokemon</th>
				<th width="50">Nível</th>
				<th width="100">Check:</th>
			</tr>
		</thead>
		<tbody><?php
		

	$check2 = DB::exQuery("SELECT * FROM tmhm_relacionados WHERE `naam`='".$_GET['name']."'")->fetch_assoc();

	
	
		#Pokemons die hij opzak heeft weergeven
		for($teller=0; $pokemon = $poke->fetch_assoc(); $teller++) {
			if ($pokemon['ei'] != 1) {
				$kan = "<img src=\"" . $static_url . "/images/icons/red.png\" />";
				$disabled = 'disabled';

				#Heeft de stone werking?
				$pegaidpokes = explode(",", $check2['relacionados']);
  foreach($pegaidpokes as $pokemonid) {
    if (!empty($pokemonid)) {
  if ($pokemonid == $pokemon['wild_id']) {
  	$kan = "<img src=\"" . $static_url . "/images/icons/green.png\" />";
  	$disabled = '';
  	}
  }
  }
  
	  
				$pokemon = pokemonei($pokemon, $txt);
				$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);
				echo '<tr>';
					if ($pokemon['ei'] != 1)	echo '<td align="center"><input type="radio" name="pokemonid" value="' . $pokemon['id'] . '" ' . $disabled . ' /></td>';
					else	echo '<td align="center"><input type="radio" id="niet' . $teller . '" name="niet" disabled /></td>';
					echo '<td align="center"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" width="32" height="32" /></td>
	    			<td>' . $pokemon['naam'] . '</td>
	    			<td align="center">' . $pokemon['level'] . '</td>';
					#Als pokemon geen baby is
					if ($pokemon['ei'] != 1)	echo '<td align="center">' . $kan . '</td>';
					else	echo '<td align="center"><img src="' . $static_url . '/images/icons/red.png" /></td>';
				echo '</tr>';
			}
		}
		?></tbody>
		<tfoot><tr><td colspan="5" align="right"><input type="submit" name="kies" value="Avançar" class="button" /></td></tr></tfoot>
	</table></div>
</form>
</body>
</html>