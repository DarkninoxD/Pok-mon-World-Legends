<?php
#include dit script als je de pagina alleen kunt zien als je ingelogd bent.
require_once('app/includes/resources/security.php');

#Als je geen pokemon bij je hebt, terug naar index.
if ($gebruiker['in_hand'] == 0)	exit(header('LOCATION: ./'));

echo addNPCBox(5, $txt['titlenpc'], $txt['textnpc']);

if ($gebruiker['rank'] >= 5) {
	 
$premium = ($gebruiker['premiumaccount'] > time())? true : false;

if (isset($_POST['shiny']) && count($_POST['pokes']) != 0 && is_array($_POST['pokes'])) {
	$goldneed = 0;
	foreach($_POST['pokes'] as $key=>$value) {
		$pokemoninfo = DB::exQuery("SELECT `pokemon_speler`.`user_id`,`pokemon_speler`.`opzak`,`pokemon_speler`.`shiny`,`pokemon_speler`.`ei`,`pokemon_wild`.`zeldzaamheid` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`id`={$value} LIMIT 1")->fetch_assoc();
		if (empty($value)) {
			$message = '<div class="red">' . $txt['alert_nothing_selected'] . '</div>';
			break;
		} else if ($pokemoninfo['ei'] == 1) {
			$message = '<div class="red">' . $txt['alert_pokemon_egg'] . '</div>';
			break;
		} else if ($pokemoninfo['user_id'] != $_SESSION['id']) {
			$message = '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
			break;
		} else if ($pokemoninfo['shiny'] == 1) {
			$message = '<div class="red">' . $txt['alert_pokemon_is_shiny'] . '</div>';
			break;
		} else if ($pokemoninfo['opzak'] != 'ja') {
			$message = '<div class="red">' . $txt['alert_not_your_team'] . '</div>';
			break;
		} else {
			if ($pokemoninfo['zeldzaamheid'] == 1)		$goldneed += (!$premium)? 20 : 15;
			else if ($pokemoninfo['zeldzaamheid'] == 2)	$goldneed += (!$premium)? 35 : 27;
			else if ($pokemoninfo['zeldzaamheid'] == 3)	$goldneed += (!$premium)? 50 : 38;
			else	$goldneed += (!$premium)? 120 : 90;
		}
	}

	if (empty($message)) {
		if ($rekening['gold'] < $goldneed)	$message = '<div class="red">' . $txt['alert_not_enough_money'] . '</div>';
		else {
			foreach($_POST['pokes'] as $key=>$value) {
				DB::exQuery("UPDATE `pokemon_speler` SET `shiny`='1' WHERE `id`={$value} AND `user_id`={$_SESSION['id']} LIMIT 1");
			}
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-{$goldneed} WHERE `acc_id`={$_SESSION['acc_id']} LIMIT 1");
			exit(header("LOCATION: ./specialists"));
		}
	}
}
if (isset($_POST['mood']) && count($_POST['pokes']) != 0 && is_array($_POST['pokes'])) {
	$pokeinfo = array();
	$goldneed = 0;
	foreach($_POST['pokes'] as $key=>$value) {
		$pokemoninfo = DB::exQuery("SELECT `pokemon_wild`.*,`pokemon_speler`.* FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`id`={$value} LIMIT 1")->fetch_assoc();

		if (empty($value)) {
			$message = '<div class="red">' . $txt['alert_nothing_selected'] . '</div>';
			break;
		} else if ($pokemoninfo['ei'] == 1) {
			$message = '<div class="red">' . $txt['alert_pokemon_egg'] . '</div>';
			break;
		} else if ($pokemoninfo['user_id'] != $_SESSION['id']) {
			$message = '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
			break;
		} else if ($pokemoninfo['opzak'] != 'ja') {
			$message = '<div class="red">' . $txt['alert_not_your_team'] . '</div>';
			break;
		} else if ($pokemoninfo['humor_change'] > 2) {
			$message = '<div class="red">Esse pokémon já fez mudou de humor o máximo de vezes.</div>';
			break;
		} else {
			# Save pokémon data
			$pokeinfo[$pokemoninfo['id']] = $pokemoninfo;

			if ($pokemon['humor_change'] == 3 OR $pokemoninfo['ei'] == 1) $goldneed = '--';
			else if ($pokemoninfo['humor_change'] == 0) $goldneed += (!$premium)? 30 : 26;
			else if ($pokemoninfo['humor_change'] == 1) $goldneed += (!$premium)? 50 : 43;
			else $goldneed += (!$premium)? 100 : 85;
		}
	}

	if (empty($message)) {
		if ($rekening['gold'] < $goldneed)	$message = '<div class="red">' . $txt['alert_not_enough_money'] . '</div>';
		else {
			foreach($_POST['pokes'] as $key=>$value) {
				$karakter = DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam`!='{$pokeinfo[$value]['karakter']}' ORDER BY RAND() LIMIT 1")->fetch_assoc();

				/* Nieuwe stats en hp berekenen
				 * Bron: http://www.upokecenter.com/games/rs/guides/id.html
				 * Stats berekenen
				 * Formule Stats = int((int(int(A*2+B+int(C/4))*D/100)+5)*E)
				 */
				$attackstat		= round((((($pokeinfo[$value]['attack_iv'] + 2 * $pokeinfo[$value]['attack_base'] + floor($pokeinfo[$value]['attack_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['attack_up']) * $karakter['attack_add']);
				$defencestat	= round((((($pokeinfo[$value]['defence_iv'] + 2 * $pokeinfo[$value]['defence_base'] + floor($pokeinfo[$value]['defence_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['defence_up']) * $karakter['defence_add']) ;
				$speedstat		= round((((($pokeinfo[$value]['speed_iv'] + 2 * $pokeinfo[$value]['speed_base'] + floor($pokeinfo[$value]['speed_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['speed_up']) * $karakter['speed_add']);
				$spcattackstat	= round((((($pokeinfo[$value]['spc.attack_iv'] + 2 * $pokeinfo[$value]['spc.attack_base'] + floor($pokeinfo[$value]['spc.attack_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['spc_up']) * $karakter['spc.attack_add']);
				$spcdefencestat	= round((((($pokeinfo[$value]['spc.defence_iv'] + 2 * $pokeinfo[$value]['spc.defence_base'] + floor($pokeinfo[$value]['spc.defence_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['spc_up']) * $karakter['spc.defence_add']);
				$hpstat			= round(((($pokeinfo[$value]['hp_iv'] + 2 * $pokeinfo[$value]['hp_base'] + floor($pokeinfo[$value]['hp_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 10 + $pokeinfo[$value]['level']) + $pokeinfo[$value]['hp_up']);

				//Stats opslaan
				DB::exQuery("UPDATE `pokemon_speler` SET `humor_change`=`humor_change`+1,`karakter`='{$karakter['karakter_naam']}',`levenmax`={$hpstat},`leven`={$hpstat},`attack`={$attackstat},`defence`={$defencestat},`speed`={$speedstat},`spc.attack`={$spcattackstat},`spc.defence`={$spcdefencestat} WHERE `id`={$value} AND `user_id`={$_SESSION['id']} LIMIT 1");
			}
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-{$goldneed} WHERE `acc_id`={$_SESSION['acc_id']} LIMIT 1");
			exit(header("LOCATION: ./specialists"));
		}
	}
}
if (isset($_POST['mood2']) && count($_POST['pokes']) != 0 && is_array($_POST['pokes'])) {
	$pokeinfo = array();
	$goldneed = 0;
	foreach($_POST['pokes'] as $key=>$value) {
		$pokemoninfo = DB::exQuery("SELECT `pokemon_wild`.*,`pokemon_speler`.* FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`id`={$value} LIMIT 1")->fetch_assoc();

		if (empty($value)) {
			$message = '<div class="red">' . $txt['alert_nothing_selected'] . '</div>';
			break;
		} else if ($pokemoninfo['ei'] == 1) {
			$message = '<div class="red">' . $txt['alert_pokemon_egg'] . '</div>';
			break;
		} else if ($pokemoninfo['user_id'] != $_SESSION['id']) {
			$message = '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
			break;
		} else if ($pokemoninfo['opzak'] != 'ja') {
			$message = '<div class="red">' . $txt['alert_not_your_team'] . '</div>';
			break;
		} else if ($pokemoninfo['humor_change'] > 2) {
			$message = '<div class="red">Esse pokémon mudou de humor o máximo de vezes.</div>';
			break;
		} else {
			# Save pokémon data
			$pokeinfo[$pokemoninfo['id']] = $pokemoninfo;

			if ($pokemon['humor_change'] == 3 OR $pokemoninfo['ei'] == 1) $goldneed = '--';
			else if ($pokemoninfo['humor_change'] == 0) $goldneed += 50;
			else if ($pokemoninfo['humor_change'] == 1) $goldneed += 80;
			else $goldneed += 130;
			
			
		}
	}

	if (empty($message)) {
		if ($rekening['gold'] < $goldneed)	$message = '<div class="red">' . $txt['alert_not_enough_money'] . '</div>';
		else {
			foreach($_POST['pokes'] as $key=>$value) {
				
				
				
				// UP OU DOWN (AUMENTAR OU DIMINIUIR UM ATRIBUTO)
				// attack / defense / spatk / spdef / speed

				
				if ($_POST['change'] == 'up') {
				//UP
				// ATTACK = hardy / lonely / brave / adamant / naughty
				// DEFENSE = bold / docile / relaxed / impish / lax
				// SPATK = modest / mild / quiet / bashful / rash
				// SPDEF =  calm / gentle / sassy / careful / quirky
				// SPEED = timid / hasty / serious / jolly / naive
				//UP
				
			    if ($_POST['atribute'] == 'attack') {
					
				$input = array("hardy","lonely","brave","adamant","naughty");
				
			    } else if ($_POST['atribute'] == 'defense') {
				
				$input = array("bold","docile","relaxed","impish","lax");
				
				} else if ($_POST['atribute'] == 'spatk') {
				
				$input = array("modest","mild","quiet","bashful","rash");
				
				} else if ($_POST['atribute'] == 'spdef') {
				
				$input = array("calm","gentle","sassy","careful","quirky");
				
				} else {
				
				$input = array("timid","hasty","serious","jolly","naive");
				
				} 
				
				} else {
				//DOWN
				// ATTACK = hardy / bold / timid / modest / calm 
				// DEFENSE = lonely / docile / hasty / mild / gentle
				// SPATK = adamant / impish / jolly / bashful / careful
				// SPDEF = naughty / lax / naive / rash / quirky
				// SPEED = brave / relaxed / serious / quiet / sassy
				//DOWN
				
			    if ($_POST['atribute'] == 'attack') {
					
				$input = array("hardy","bold","timid","modest","calm");
				
			    } else if ($_POST['atribute'] == 'defense') {
				
				$input = array("lonely","docile","hasty","mild","gentle");
				
				} else if ($_POST['atribute'] == 'spatk') {
				
				$input = array("adamant","impish","jolly","bashful","careful");
				
				} else if ($_POST['atribute'] == 'spdef') {
				
				$input = array("naughty","lax","naive","rash","quirky");
				
				} else {
				
				$input = array("brave","relaxed","serious","quiet","sassy");
				
				} 
				
				}
				$remover = array($pokeinfo[$value]['karakter']);

				$input = array_diff($input, $remover);
				$karakterpremium = $input[array_rand($input)];

				$karakter = DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam`='{$karakterpremium}' LIMIT 1")->fetch_assoc();

				
				
				
				/* Nieuwe stats en hp berekenen
				 * Bron: http://www.upokecenter.com/games/rs/guides/id.html
				 * Stats berekenen
				 * Formule Stats = int((int(int(A*2+B+int(C/4))*D/100)+5)*E)
				 */
				$attackstat		= round((((($pokeinfo[$value]['attack_iv'] + 2 * $pokeinfo[$value]['attack_base'] + floor($pokeinfo[$value]['attack_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['attack_up']) * $karakter['attack_add']);
				$defencestat	= round((((($pokeinfo[$value]['defence_iv'] + 2 * $pokeinfo[$value]['defence_base'] + floor($pokeinfo[$value]['defence_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['defence_up']) * $karakter['defence_add']) ;
				$speedstat		= round((((($pokeinfo[$value]['speed_iv'] + 2 * $pokeinfo[$value]['speed_base'] + floor($pokeinfo[$value]['speed_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['speed_up']) * $karakter['speed_add']);
				$spcattackstat	= round((((($pokeinfo[$value]['spc.attack_iv'] + 2 * $pokeinfo[$value]['spc.attack_base'] + floor($pokeinfo[$value]['spc.attack_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['spc_up']) * $karakter['spc.attack_add']);
				$spcdefencestat	= round((((($pokeinfo[$value]['spc.defence_iv'] + 2 * $pokeinfo[$value]['spc.defence_base'] + floor($pokeinfo[$value]['spc.defence_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['spc_up']) * $karakter['spc.defence_add']);
				$hpstat			= round(((($pokeinfo[$value]['hp_iv'] + 2 * $pokeinfo[$value]['hp_base'] + floor($pokeinfo[$value]['hp_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 10 + $pokeinfo[$value]['level']) + $pokeinfo[$value]['hp_up']);

				//Stats opslaan
				DB::exQuery("UPDATE `pokemon_speler` SET `humor_change`=`humor_change`+1,`karakter`='{$karakter['karakter_naam']}',`levenmax`={$hpstat},`leven`={$hpstat},`attack`={$attackstat},`defence`={$defencestat},`speed`={$speedstat},`spc.attack`={$spcattackstat},`spc.defence`={$spcdefencestat} WHERE `id`={$value} AND `user_id`={$_SESSION['id']} LIMIT 1");
			}
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-{$goldneed} WHERE `acc_id`={$_SESSION['acc_id']} LIMIT 1");
			exit(header("LOCATION: ./specialists"));
		}
	}
}
if (isset($_POST['mood3']) && count($_POST['pokes']) != 0 && is_array($_POST['pokes']) && isset($_POST['atribute'])) {
	$pokeinfo = array();
	$goldneed = 0;
	foreach($_POST['pokes'] as $key=>$value) {
		$pokemoninfo = DB::exQuery("SELECT `pokemon_wild`.*,`pokemon_speler`.* FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`id`={$value} LIMIT 1")->fetch_assoc();

		if (empty($value)) {
			$message = '<div class="red">' . $txt['alert_nothing_selected'] . '</div>';
			break;
		} else if ($pokemoninfo['ei'] == 1) {
			$message = '<div class="red">' . $txt['alert_pokemon_egg'] . '</div>';
			break;
		} else if ($pokemoninfo['user_id'] != $_SESSION['id']) {
			$message = '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
			break;
		} else if ($pokemoninfo['opzak'] != 'ja') {
			$message = '<div class="red">' . $txt['alert_not_your_team'] . '</div>';
			break;
		} else if ($pokemoninfo['humor_change'] > 0) {
			$message = '<div class="red">Esse pokémon já fez alguma troca de humor.</div>';
			break;
		} else {
			# Save pokémon data
			$pokeinfo[$pokemoninfo['id']] = $pokemoninfo;

			if ($pokemon['humor_change'] > 0 OR $pokemoninfo['ei'] == 1) $goldneed = '--';
			else $goldneed += 250;		
			
		}
	}

	if (empty($message)) {
		$karakter = DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam`='$_POST[atribute]' LIMIT 1")->fetch_assoc();
		if ($rekening['gold'] < $goldneed)	$message = '<div class="red">' . $txt['alert_not_enough_money'] . '</div>';
		else if (empty($karakter)) { $message = '<div class="red">Este humor não existe!</div>';
		} else {
			foreach($_POST['pokes'] as $key=>$value) {
				$attackstat		= round((((($pokeinfo[$value]['attack_iv'] + 2 * $pokeinfo[$value]['attack_base'] + floor($pokeinfo[$value]['attack_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['attack_up']) * $karakter['attack_add']);
				$defencestat	= round((((($pokeinfo[$value]['defence_iv'] + 2 * $pokeinfo[$value]['defence_base'] + floor($pokeinfo[$value]['defence_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['defence_up']) * $karakter['defence_add']) ;
				$speedstat		= round((((($pokeinfo[$value]['speed_iv'] + 2 * $pokeinfo[$value]['speed_base'] + floor($pokeinfo[$value]['speed_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['speed_up']) * $karakter['speed_add']);
				$spcattackstat	= round((((($pokeinfo[$value]['spc.attack_iv'] + 2 * $pokeinfo[$value]['spc.attack_base'] + floor($pokeinfo[$value]['spc.attack_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['spc_up']) * $karakter['spc.attack_add']);
				$spcdefencestat	= round((((($pokeinfo[$value]['spc.defence_iv'] + 2 * $pokeinfo[$value]['spc.defence_base'] + floor($pokeinfo[$value]['spc.defence_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 5) + $pokeinfo[$value]['spc_up']) * $karakter['spc.defence_add']);
				$hpstat			= round(((($pokeinfo[$value]['hp_iv'] + 2 * $pokeinfo[$value]['hp_base'] + floor($pokeinfo[$value]['hp_ev'] / 4)) * $pokeinfo[$value]['level'] / 100) + 10 + $pokeinfo[$value]['level']) + $pokeinfo[$value]['hp_up']);

				//Stats opslaan
				DB::exQuery("UPDATE `pokemon_speler` SET `humor_change`=`humor_change`+3,`karakter`='{$karakter['karakter_naam']}',`levenmax`={$hpstat},`leven`={$hpstat},`attack`={$attackstat},`defence`={$defencestat},`speed`={$speedstat},`spc.attack`={$spcattackstat},`spc.defence`={$spcdefencestat} WHERE `id`={$value} AND `user_id`={$_SESSION['id']} LIMIT 1");
			}
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-{$goldneed} WHERE `acc_id`={$_SESSION['acc_id']} LIMIT 1");
			exit(header("LOCATION: ./specialists"));
		}
	}
}
if (isset($_POST['naam']) && count($_POST['pokes']) != 0 && is_array($_POST['pokes'])) {
	$silverneed = 0;
	foreach($_POST['pokes'] as $key=>$value) {
		$pokemoninfo = DB::exQuery("SELECT `pokemon_wild`.*,`pokemon_speler`.* FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`id`={$value} LIMIT 1")->fetch_assoc();
		$pokemoninfo = pokemonei($pokemoninfo, $txt);
		$pokemoninfo['naam'] = pokemon_naam($pokemoninfo['naam'], $pokemoninfo['roepnaam']);

		if (empty($value)) {
			$message = '<div class="red">' . $txt['alert_nothing_selected'] . '</div>';
			break;
		} else if (strlen(trim($_POST['name'][$value])) < 4 || strlen(trim($_POST['name'][$value])) > 12 AND $_POST['remove']  != "remove") {
			$message = '<div class="red">' . $txt['alert_name_too_long'] . '</div>';
			break;
		} else if ($pokemoninfo['naam'] == $_POST['name'][$value] AND $_POST['remove']  != "remove") {
			$message = '<div class="red">' . $txt['alert_name_equal'] . '</div>';
			break;
		} else if (!preg_match("/^([a-zA-Z0-9]+)$/", $_POST['name'][$value]) AND $_POST['remove']  != "remove") {
		$message = '<div class="red">O nome não pode conter caracters especiais!</div>';
		break;
		/*} else if (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `roepnaam`='{$_POST['name'][$value]}' LIMIT 1")->num_rows != 0) {
			$message = '<div class="red">' . $txt['alert_name_exists'] . '</div>';
			break;*/
		} else if ($pokemoninfo['ei'] == 1) {
			$message = '<div class="red">' . $txt['alert_pokemon_egg'] . '</div>';
			break;
		} else if ($pokemoninfo['user_id'] != $_SESSION['id']) {
			$message = '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
			break;
		} else if ($pokemoninfo['opzak'] != 'ja') {
			$message = '<div class="red">' . $txt['alert_not_your_team'] . '</div>';
			break;
		} else {
			#Hoeveel gold nodig?
			if (!$premium) {
				if ($pokemoninfo['naam_changes'] != 0) {
					if ($pokemoninfo['zeldzaamheid'] == 1)		$silverneed += 250 * $pokemoninfo['naam_changes'];
					else if ($pokemoninfo['zeldzaamheid'] == 2)	$silverneed += 350 * $pokemoninfo['naam_changes'];
					else if ($pokemoninfo['zeldzaamheid'] == 3)	$silverneed += 400 * $pokemoninfo['naam_changes'];
					else	$silverneed += 600 * $pokemoninfo['naam_changes'];
				} else {
					if ($pokemoninfo['zeldzaamheid'] == 1)		$silverneed += 250;
					else if ($pokemoninfo['zeldzaamheid'] == 2)	$silverneed += 350;
					else if ($pokemoninfo['zeldzaamheid'] == 3)	$silverneed += 400;
					else	$silverneed += 600;
				}
			}
		}
	}
	if (empty($message)) {
		if ($gebruiker['silver'] < $silverneed)	$message = '<div class="red">' . $txt['alert_not_enough_money'] . '</div>';
		else {
			foreach($_POST['pokes'] as $key=>$value) {
			
			if ($_POST['remove'] == "remove") {
        	DB::exQuery("UPDATE `pokemon_speler` SET `naam_changes`=`naam_changes`+1,`roepnaam`='' WHERE `id`={$value} LIMIT 1");
		} else {
        	DB::exQuery("UPDATE `pokemon_speler` SET `naam_changes`=`naam_changes`+1,`roepnaam`='{$_POST['name'][$value]}' WHERE `id`={$value} LIMIT 1");
        	}
        	
      	
			}
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-{$silverneed} WHERE `user_id`={$_SESSION['id']} LIMIT 1");
			exit(header("LOCATION: ./specialists"));
		}
	}
}
if (!empty($message))	echo $message;
?>
<div class="row" style="margin-bottom: 7px">
	<div class="box-content col" style="width: 50%; margin-right: 3px">
		<form action="./specialists" method="post"><table class="general" width="100%">
			<thead>
				<tr><th colspan="5"><?=$txt['shiny_specialist'];?></th></tr>
				<tr>
					<th width="10"><?=$txt['#'];?></th>
					<th colspan="2"><?=$txt['pokemon'];?></th>
					<th><?=$txt['level'];?></th>
					<th><?=$txt['amount'];?></th>
				</tr>
			</thead>
			<tbody><?php
			while($pokemon = $pokemon_sql->fetch_assoc()) {
				$pokemon = pokemonei($pokemon, $txt);
				$popup = pokemon_popup($pokemon, $txt);
				$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

				if ($pokemon['ei'] == 1 || $pokemon['shiny'] == 1) $goldoutput = '--';
				else if ($pokemon['zeldzaamheid'] == 1)	$goldoutput = 20;
				else if ($pokemon['zeldzaamheid'] == 2)	$goldoutput = 35;
				else if ($pokemon['zeldzaamheid'] == 3)	$goldoutput = 50;
				else $goldoutput = 120;

				$gold = (!$premium)? highamount($goldoutput) : highamount(ceil((int) $goldoutput - ((int) $goldoutput * 0.25)));

				echo '<tr>
					<td align="center"><input type="checkbox" name="pokes[]" value="' . $pokemon['id'] . '"' . ($pokemon['shiny'] == 1 || $pokemon['ei'] == 1 ? ' disabled' : '') . ' /></td>
					<td width="32px" class="tip_right-middle" title="' . $popup . '" align="center"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" /></td>
					<td>' . $pokemon['naam'] . '</td>
					<td align="center">' . $pokemon['level'] . '</td>';
					if ($pokemon['ei'] == 1 || $pokemon['shiny'] == 1)	echo '<td align="center">' . $goldoutput . '</td>';
					else	echo '<td align="center"><img src="' . $static_url . '/images/icons/gold.png" width="14px" style="vertical-align: -2px;" /> ' . $gold . '</td>';
				echo '</tr>';
			}
			$pokemon_sql->data_seek(0);
			?></tbody>
			<tfoot><tr><td colspan="5" align="right"><input type="submit" name="shiny" value="<?=$txt['buttom'];?>" class="button" /></td></tr></tfoot>
		</table></form>
	</div>
<div class="box-content col" style="width: 50%; margin-left: 3px">
	<form action="./specialists" method="post"><table class="general" width="100%">
		<thead>
			<tr><th colspan="6"><?=$txt['naam_specialist'];?></th></tr>
			<tr>
				<th width="26"><?=$txt['#'];?></th>
				<th colspan="2" width="130"><?=$txt['pokemon'];?></th>
				<th><?=$txt['level'];?></th>
				<th width="65"><?=$txt['amount'];?></th>
				<th><?=$txt['naam'];?></th>
			</tr>
		</thead>
		<tbody><?php
		while($pokemon = $pokemon_sql->fetch_assoc()) {
			$pokemon = pokemonei($pokemon, $txt);
			$popup = pokemon_popup($pokemon, $txt);
			$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

			if ($pokemon['ei'] == 1)		$goldoutput = '--';
			else if ($pokemon['zeldzaamheid'] == 1) $silveroutput = 250;
			else if ($pokemon['zeldzaamheid'] == 2)	$silveroutput = 350;
			else if ($pokemon['zeldzaamheid'] == 3)	$silveroutput = 400;
			else	$silveroutput = 600;
			if ($pokemon['naam_changes'] != 0)	$silveroutput *= $pokemon['naam_changes'];

			$silver = (!$premium)? highamount($silveroutput) : 'Grátis';

			echo '<tr>
				<td align="center"><input type="checkbox" name="pokes[]" value="' . $pokemon['id'] . '"' . ($pokemon['ei'] == 1 ? ' disabled' : '') . ' /></td>
				<td width="32px" class="tip_right-middle" title="' . $popup . '" align="center"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" /></td>
				<td>' . $pokemon['naam'] . '</td>
				<td align="center">' . $pokemon['level'] . '</td>';
				if ($pokemon['ei'] == 1)	echo '<td align="center">' . $goldoutput . '</td>';
				else	echo '<td align="center"><img src="' . $static_url . '/images/icons/silver.png" width="14px" style="vertical-align: -2px;" /> ' . $silver . '</td>';
				echo '<td align="center"><input type="text" name="name[' . $pokemon['id'] . ']" value="' . rtrim(strip_tags($pokemon['roepnaam'])).'" maxlength="12" style="width: 140px;" /></td>';
			echo '</tr>';
		}
		$pokemon_sql->data_seek(0);
		?></tbody>
		<tfoot><tr><td colspan="6" align="right"><input type="checkbox" name="remove" id="remove" value="remove"> Voltar nome padrão <input type="submit" name="naam" value="<?=$txt['buttom'];?>" class="button" /></td></tr></tfoot>
	</table></form>
</div>
</div>
<div class="row">
<div class="box-content col" style="width: 50%; margin-right: 3px">
	<form action="./specialists" method="post"><table class="general" width="100%">
		<thead>
			<tr><th colspan="5"><?=$txt['mood_specialist'];?></th></tr>
			<tr>
				<th width="10"><?=$txt['#'];?></th>
				<th colspan="2"><?=$txt['pokemon'];?></th>
				<th><?=$txt['level'];?></th>
				<th><?=$txt['amount'];?></th>
			</tr>
		</thead>
		<tbody><?php
		while($pokemon = $pokemon_sql->fetch_assoc()) {
			$pokemon = pokemonei($pokemon, $txt);
			$popup = pokemon_popup($pokemon, $txt);
			$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

			if ($pokemon['humor_change'] == 3 OR $pokemoninfo['ei'] == 1) $goldoutput = '--';
			else if ($pokemon['humor_change'] == 0) $goldoutput = 30;
			else if ($pokemon['humor_change'] == 1) $goldoutput = 50;
			else $goldoutput = 100;

			$gold = (!$premium)? highamount($goldoutput) : highamount(ceil((int) $goldoutput - ((int) $goldoutput * 0.15)));

			echo '<tr>
				<td align="center"><input type="checkbox" name="pokes[]" value="' . $pokemon['id'] . '"' . ($pokemon['ei'] == 1 ? ' disabled' : '') . ' /></td>
				<td width="32px" class="tip_right-middle" title="' . $popup . '" align="center"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" /></td>
				<td>' . $pokemon['naam'] . '</td>
				<td align="center">' . $pokemon['level'] . '</td>';
				if ($pokemon['humor_change'] == 3 OR $pokemon['ei'] == 1)	echo '<td align="center">' . $goldoutput . '</td>';
				else	echo '<td align="center"><img src="' . $static_url . '/images/icons/gold.png" width="14px" style="vertical-align: -2px;" /> ' . $gold . '</td>';
			echo '</tr>';
		}
		$pokemon_sql->data_seek(0);
		?></tbody>
		<tfoot><tr><td colspan="5" align="right"><input type="submit" name="mood" value="<?=$txt['buttom'];?>" class="button" /></td></tr></tfoot>
	</table></form>
</div>
<div class="box-content col" style="width: 50%; margin-left: 3px">
	<form action="./specialists" method="post"><table class="general" width="100%">
		<thead>
			<tr><th colspan="5"><?=$txt['mood_specialist'];?> Premium</th></tr>
			<tr>
				<th width="10"><?=$txt['#'];?></th>
				<th colspan="2"><?=$txt['pokemon'];?></th>
				<th><?=$txt['level'];?></th>
				<th><?=$txt['amount'];?></th>
			</tr>
		</thead>
		<tbody><?php
		while($pokemon = $pokemon_sql->fetch_assoc()) {
			$pokemon = pokemonei($pokemon, $txt);
			$popup = pokemon_popup($pokemon, $txt);
			$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

			if ($pokemon['humor_change'] == 3 OR $pokemoninfo['ei'] == 1) $goldoutput = '--';
			else if ($pokemon['humor_change'] == 0) $goldoutput = 50;
			else if ($pokemon['humor_change'] == 1) $goldoutput = 80;
			else $goldoutput = 130;

			echo '<tr>
				<td align="center"><input type="checkbox" name="pokes[]" value="' . $pokemon['id'] . '"' . ($pokemon['ei'] == 1 ? ' disabled' : '') . ' /></td>
				<td width="32px" class="tip_right-middle" title="' . $popup . '" align="center"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" /></td>
				<td>' . $pokemon['naam'] . '</td>
				<td align="center">' . $pokemon['level'] . '</td>';
				if ($pokemon['humor_change'] == 3 OR $pokemon['ei'] == 1)	echo '<td align="center">' . $goldoutput . '</td>';
				else	echo '<td align="center"><img src="' . $static_url . '/images/icons/gold.png" width="14px" style="vertical-align: -2px;" /> ' . highamount($goldoutput) . '</td>';
			echo '</tr>';
		}
		$pokemon_sql->data_seek(0);
		?></tbody>
		<tfoot><tr><td colspan="5" align="right"> 
		<select name="change">
                        <option value="up">Aumentar</option>
                        <option value="down">Diminuir</option>
                    </select>
                    &nbsp;&nbsp;
                    <select name="atribute">
                        <option value="attack">Attack</option>
                        <option value="defense">Defense</option>
                        <option value="spatk">Sp. Atk</option>
                        <option value="spdef">Sp. Def</option>
                        <option value="speed">Speed</option>
                    </select>
					<input type="submit" name="mood2" value="<?=$txt['buttom'];?>" class="button" /></td></tr>
					<tr><td colspan="5">Mudança mais delicada, porém mais eficaz, podemos definir se um certo atributo irá aumentar ou diminuir, o que aumenta as chances de obter o humor desejado. <br>
					<b>Lembrando que os humores neutros são válidos, pelo fato deles serem justamente neutros por aumentarem e diminuírem o mesmo atributo.</b></td></tr></tfoot>
	</table></form>
</div>
	</div>

<div class="row" style="margin-top: 7px">
<div class="box-content col" style="width: 50%; margin-right:3px">
	<form action="./specialists" method="post"><table class="general" width="100%">
		<thead>
			<tr><th colspan="5"><?=$txt['mood_specialist'];?> Profissional</th></tr>
			<tr>
				<th width="10"><?=$txt['#'];?></th>
				<th colspan="2"><?=$txt['pokemon'];?></th>
				<th><?=$txt['level'];?></th>
				<th><?=$txt['amount'];?></th>
			</tr>
		</thead>
		<tbody><?php
		while($pokemon = $pokemon_sql->fetch_assoc()) {
			$pokemon = pokemonei($pokemon, $txt);
			$popup = pokemon_popup($pokemon, $txt);
			$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

			if ($pokemon['humor_change'] > 0 OR $pokemon['ei'] == 1) $goldoutput = '--';
			else $goldoutput = 250;

			echo '<tr>
				<td align="center"><input type="checkbox" name="pokes[]" value="' . $pokemon['id'] . '"' . ($pokemon['ei'] == 1 ? ' disabled' : '') . ' /></td>
				<td width="32px" class="tip_right-middle" title="' . $popup . '" align="center"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" /></td>
				<td>' . $pokemon['naam'] . '</td>
				<td align="center">' . $pokemon['level'] . '</td>';
				if ($pokemon['humor_change'] > 0 OR $pokemon['ei'] == 1)	echo '<td align="center">' . $goldoutput . '</td>';
				else	echo '<td align="center"><img src="' . $static_url . '/images/icons/gold.png" width="14px" style="vertical-align: -2px;" /> ' . highamount($goldoutput) . '</td>';
			echo '</tr>';
		}
		$pokemon_sql->data_seek(0);
		?></tbody>
		<tfoot><tr><td colspan="5" align="right"> 
                    <select name="atribute">
						<?php
							$humores = DB::exQuery("SELECT * FROM `karakters` ORDER BY `karakter_naam`");
							while($h = $humores->fetch_assoc()) {
								echo '<option value="'.$h['karakter_naam'].'">'.ucfirst($h['karakter_naam']).'</option>';
							}
						?>
                    </select>
					<input type="submit" name="mood3" value="<?=$txt['buttom'];?>" class="button" /></td></tr>
					<tr><td colspan="5">Mudança mais cara, porém mais adaptativa, você pode escolher qual humor seu Pokémon terá. <br>
					<b>Lembrando que o Pokémon não pode ter sofrido alguma alteração de humor anteriormente, além de não ser mais possível trocar de humor após a escolha.</b></td></tr></tfoot>
	</table></form>
</div>
</div>

<?php } else { 
	echo '<div class="red">RANK MÍNIMO PARA UTILIZAR DAS FUNCIONALIDADES DOS ESPECIALISTAS: 5 - First Coach. CONTINUE UPANDO PARA LIBERAR!</div>';
} ?>