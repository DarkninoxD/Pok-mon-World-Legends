<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");
error_reporting(0);

#Gegevens laden van de des betreffende pokemon
$pokemon = DB::exQuery("SELECT pokemon_wild.wild_id, pokemon_wild.naam, pokemon_wild.groei, pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id WHERE pokemon_speler.id='".$evolueren['pokemonid']."'")->fetch_assoc();

$update = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='".$evolueren['nieuw_id']."'")->fetch_assoc();

#als er op de doorgaan knop gedrukt word
if (isset($_POST['acceptevolutie'])) {
	$tekst = "<div class='green'>". sprintf($txt['accepted'], $pokemon['naam'], $update['naam']). "</div>";
	$button = false;

	#Pokemon opslaan als in bezit
	update_pokedex($update['wild_id'], $pokemon['wild_id'], 'evo');

	#Nieuwe stats opslaan
	#Nieuwe level word
	$levelnieuw = ++$pokemon['level'];
	if ($levelnieuw > 100)	$levelnieuw = 100;
	$info = DB::exQuery("SELECT experience.punten, karakters.* FROM experience INNER JOIN karakters WHERE experience.soort='".$pokemon['groei']."' AND experience.level='".$levelnieuw."' AND karakters.karakter_naam='".$pokemon['karakter']."'")->fetch_assoc();

	$attackstat		= round((((($pokemon['attack_iv'] + 2 * $update['attack_base'] + floor($pokemon['attack_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['attack_up']) * $info['attack_add']);
	$defencestat	= round((((($pokemon['defence_iv'] + 2 * $update['defence_base'] + floor($pokemon['defence_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['defence_up']) * $info['defence_add']) ;
	$speedstat		= round((((($pokemon['speed_iv'] + 2 * $update['speed_base'] + floor($pokemon['speed_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['speed_up']) * $info['speed_add']);
	$spcattackstat	= round((((($pokemon['spc.attack_iv'] + 2 * $update['spc.attack_base'] + floor($pokemon['spc.attack_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['spc_up']) * $info['spc.attack_add']);
	$spcdefencestat	= round((((($pokemon['spc.defence_iv'] + 2 * $update['spc.defence_base'] + floor($pokemon['spc.defence_ev'] / 4)) * $pokemon['level'] / 100) + 5) + $pokemon['spc_up']) * $info['spc.defence_add']);
	$hpstat			= round(((($pokemon['hp_iv'] + 2 * $update['hp_base'] + floor($pokemon['hp_ev'] / 4)) * $pokemon['level'] / 100) + 10 + $pokemon['level']) + $pokemon['hp_up']);
	
    $ability = explode(',', $update['ability'])[rand(0, (sizeof($ability) - 1))];

	#Pokemon gegevens en Stats opslaan
	DB::exQuery("UPDATE `pokemon_speler` SET `wild_id`='".$update['wild_id']."', `attack`='".$attackstat."', `defence`='".$defencestat."', `speed`='".$speedstat."', `spc.attack`='".$spcattackstat."', `spc.defence`='".$spcdefencestat."', `levenmax`='".$hpstat."', `leven`='".$hpstat."', `ability`='".$ability."' WHERE `id`='".$pokemon['id']."'");

	#Check if more pokemon should evolve
	$current = array_pop($_SESSION['used']);      

	$count = 0;
	$sql = DB::exQuery("SELECT pokemon_wild.naam, pokemon_speler.id, pokemon_speler.wild_id, pokemon_speler.roepnaam, pokemon_speler.level, pokemon_speler.expnodig, pokemon_speler.exp FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id WHERE pokemon_speler.id='".$current."'");
	while($select = $sql->fetch_assoc()) {
		#Change name for male and female
		$select['naam_goed'] = pokemon_naam($select['naam'],$select['roepnaam']);
		if ($select['level'] < 100) {
			#Load data from pokemon living grows Leveling table
			$levelensql = DB::exQuery("SELECT `id`, `level`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval` FROM `levelen` WHERE `wild_id`='".$select['wild_id']."' AND `level`>'".$_SESSION['lvl_old']."' AND `level`<='".$select['level']."' ORDER BY id ASC");
			#Voor elke actie kijken als het klopt.
			while($levelen = $levelensql->fetch_assoc()) {
				#als de actie een aanval leren is
				if ($levelen['wat'] == "att") {
					#Kent de pokemon deze aanval al
					if ($select['aanval_1'] != $levelen['aanval'] && $select['aanval_2'] != $levelen['aanval'] && $select['aanval_3'] != $levelen['aanval'] && $select['aanval_4'] != $levelen['aanval']) {
						unset($_SESSION['evolueren']);
						if ($levelen['level'] > $select['level'])	break;
						$_SESSION['aanvalnieuw'] = base64_encode($select['id']."/".$levelen['aanval']);
						++$count;
						$_SESSION['lvl_old'] = $levelen['level'];
						array_push($_SESSION['used'], $select['id']);
						break;
					}
				} else if ($levelen['wat'] == "evo") {	#Gaat de pokemon evolueren
					#The level is greater than or equal to the level that is required? To another page
					if ($levelen['level'] <= $select['level'] || ($levelen['trade'] == 1 && $select['trade'] == "1.5")) {
						unset($_SESSION['aanvalnieuw']);
						if ($levelen['level'] > $select['level'])	break;
						$_SESSION['evolueren'] = base64_encode($select['id']."/".$levelen['nieuw_id']);
						++$count;
						$_SESSION['lvl_old'] = $levelen['level'];
						array_push($_SESSION['used'], $select['id']);
						break;
					}
				}
			}
			if ($count != 0)	break;
		}
	}
	if ($count == 0)	unset($_SESSION['evolueren']);  

	#Event taal pack includen
	$eventlanguage = GetEventLanguage();
	require_once('language/events/language-events-'.$eventlanguage.'.php');

	#Melding geven aan de uitdager
	$event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> '.$pokemon['naam'].' '.str_replace('%s', '', $txt['event_is_evolved_in']).' <a href="./pokedex&poke='.$update['wild_id'].'">'.$update['naam'].'</a>.';
	DB::exQuery("INSERT INTO `gebeurtenis` (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '".$_SESSION['id']."', '".$event."', '0')");
} else if (isset($_POST['stopevolutie'])) {	#Als er op de stop knop gedrukt word
	$message = "<div class='red'>". sprintf($txt['stopped'], $pokemon['naam']). "</div>";
	$button = false;

	#Checken als meer pokemon moet evolueren
	$current = array_pop($_SESSION['used']);      

	$count = 0;
	$sql = DB::exQuery("SELECT pokemon_wild.naam, pokemon_speler.id, pokemon_speler.wild_id, pokemon_speler.roepnaam, pokemon_speler.level, pokemon_speler.trade, pokemon_speler.expnodig, pokemon_speler.exp FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id WHERE pokemon_speler.id='".$current."'");
	while($select = $sql->fetch_assoc()) {
		#Change name for male and female
		$select['naam_goed'] = pokemon_naam($select['naam'],$select['roepnaam']);
		if ($select['level'] < 101) {
			#Gegevens laden van pokemon die leven groeit uit levelen tabel
			$levelensql = DB::exQuery("SELECT `id`, `level`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval` FROM `levelen` WHERE `wild_id`='".$select['wild_id']."' AND `level`>'".$_SESSION['lvl_old']."' ORDER BY id ASC");
			#Voor elke actie kijken als het klopt.
			while($levelen = $levelensql->fetch_assoc()) {
				#als de actie een aanval leren is
				if ($levelen['wat'] == "att") {
					#Kent de pokemon deze aanval al
					if ($select['aanval_1'] != $levelen['aanval'] && $select['aanval_2'] != $levelen['aanval'] && $select['aanval_3'] != $levelen['aanval'] && $select['aanval_4'] != $levelen['aanval']) {
						unset($_SESSION['evolueren']);
						$_SESSION['aanvalnieuw'] = base64_encode($select['id']."/".$levelen['aanval']);
						++$count;
						$_SESSION['lvl_old'] = $levelen['level'];
						array_push($_SESSION['used'], $select['id']);
						break;
					}
				} else if ($levelen['wat'] == "evo") {	#Does the pokemon evolve
					#The level is greater than or equal to the level that is required? To another page
					if ($levelen['level'] <= $select['level'] || ($levelen['trade'] == 1 && $select['trade'] == "1.5")) {
						$_SESSION['evolueren'] = base64_encode($select['id']."/".$levelen['nieuw_id']);
						++$count;
						$_SESSION['lvl_old'] = $levelen['level'];
						array_push($_SESSION['used'], $select['id']);
						break;
					}
				}
			}
			if ($count != 0)	break;
		}
	}
	if ($count == 0)	unset($_SESSION['evolueren']);  
} else $button = true;


$title = 'Seu pokémon está evoluindo!';
$text = 'Seu pokémon chegou em um novo estágio, ele agora irá evoluir para ' . $update['naam'] . '. Caso não queira que está evolução aconteça clique em <b>"PARAR EVOLUÇÂO"</b>, caso contrário clique em <b>"EVOLUIR!"</b>.';
echo addNPCBox(2, $title, $text);

if (!empty($message))	echo $message;
?>
<script src="<?=$static_url;?>/javascripts/poke.evolve.js"></script>
<?php
    $sprite_1 = $pokemon['shiny'] == 1 ? 'shiny' : 'pokemon'.'/'.$pokemon['wild_id'].'.gif';
    $sprite_2 = $pokemon['shiny'] == 1 ? 'shiny' : 'pokemon'.'/'.$update['wild_id'].'.gif';
?>
<div class="separator"></div>
<form method="post">
	<div class="box-content" style="width: 100%; margin: 0 auto;"><table style="width: 100%" class="general">
		<thead><tr><th colspan="3"><?=sprintf($txt['evolueren'], $pokemon['naam'], $update['naam']);?></th></tr></thead>
		<?php if (!$button) { ?>
			<tbody>
			<tr>
				<td style="width: 100%">
					<center><img src="<?=$static_url?>/images/<?=$sprite_1;?>" class="pokemon" id="evolution"/></center>
				</td>
			</tr>
		</tbody>
		<?php if (isset($_POST['acceptevolutie'])) { ?>
		<script>
		   $('#evolution').wlEvolve('<?=$static_url?>'+'/images/'+'<?=$sprite_1?>', '<?=$static_url?>'+'/images/'+'<?=$sprite_2?>');
		    wlSound('evolve', <?=$gebruiker['volume']?>, false);
		</script>
		<?php } ?>
		<?php } ?>
		<?php if ($button) { ?>
		<tbody><tr>
			<td style="height: 125px; width: 180px; background: url(<?=$static_url;?>/images/<?php if ($pokemon['shiny'] == 0) echo 'pokemon'; else echo 'shiny'; ?>/<?php echo $pokemon['wild_id']; ?>.gif) center center no-repeat;"</td>
			<td width="30" align="center"><img src="<?=$static_url;?>/images/icons/pijl_rechts.png" width="16" height="16" /></td>
			<td style="height: 125px; width: 180px; background: url(<?=$static_url;?>/images/<?php if ($pokemon['shiny'] == 0) echo 'pokemon'; else echo 'shiny'; ?>/<?php echo $update['wild_id']; ?>.gif) center center no-repeat;"</td>
		</tr></tbody>
		<tfoot><tr>
			<td align="center"><input type="submit" name="stopevolutie" value="<?=$txt['stop'];?>" class="button" /></td>
			<td>&nbsp;</td>
			<td align="center"><input type="submit" name="acceptevolutie" value="<?=$txt['accept'];?>" class="button" /></td>
		</tr></tfoot>
		<?php } ?>
	</table></div>
</form>