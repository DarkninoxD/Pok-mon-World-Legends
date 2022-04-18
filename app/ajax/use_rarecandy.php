<?php
$txt['event_is_level_up'] = '<b>%s</b> passou de nível!';
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
$continue = false;
if ($gebruiker_item[$_GET['name']] <= 0) {
?>
<script type="text/javascript">
	parent.$.fn.colorbox.close();
</script>
<?php
} else {
	if (isset($_POST['spcitem']) && isset($_POST['pokemonid']) && is_numeric($_POST['num_' . $_POST['pokemonid']])) {
		$num_ = round($_POST['num_' . $_POST['pokemonid']]);

		//Pokemon gegevens laden
		$pokemon = DB::exQuery("SELECT pokemon_wild.*,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id='".$_POST['pokemonid']."'")->fetch_assoc();

		if ($gebruiker_item[$_GET['name']] < $num_)	$error = 'Você não possui está quantidade!';
		else if ($pokemon['level'] + $num_ > 100)	$error = 'Você não pode usar esta quantidade';
		else if ($num_ < 0.9) $error = "Digite um valor.";
		else {
			$levelnieuw = $pokemon['level'];
			for($evo=$pokemon['level'];$evo<($pokemon['level'] + $num_);++$evo) {
				if ($pokemon['level'] > 100)	break;
				else	++$levelnieuw;
			}
			//Script aanroepen dat nieuwe stats berekent
			nieuwestats($pokemon, $levelnieuw, $pokemon['expnodig']);

			//Script aanroepen dat berekent als pokemon evolueert of een aanval leert
			$toestemming = levelgroei($levelnieuw, $pokemon);

			//Gebeurtenis maken.
			$pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam'],$pokemon['icon']);
			$pokemonnaam = htmlspecialchars($pokemon['naam'], ENT_QUOTES);

			#Event taal pack includen
			$eventlanguage = GetEventLanguage();
			require_once('language/events/language-events-'.$eventlanguage.'.php');
			$textoeventrare = sprintf($txt['event_is_level_up'], $pokemonnaam);
			$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> '.$textoeventrare.'';

			$continue = true;

			DB::exQuery("INSERT INTO gebeurtenis (datum, ontvanger_id, bericht, gelezen) VALUES (NOW(), '".$_SESSION['id']."', '".$event."', '0')");
			#Item weg
			DB::exQuery("UPDATE `gebruikers_item` SET `".$_POST['item']."`=`".$_POST['item']."`-'".$num_."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
		}
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
<form action="./ajax.php?act=<?=$_GET['act'];?>&amp;name=<?=$_GET['name'];?>" method="post">
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="5">Escolha qual pokémon irá receber a <?=$_GET['name'];?>.</th></tr>
			<tr>
				<th style="max-width: 10px;">#</th>
				<th style="max-width: 80px;">Quantidade</th>
				<th colspan="2">Pokémon</th>
				<th style="max-width: 50px;">Nível</th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($error)) { echo '<tr><td colspan="10">' . $error . '</td></tr>'; } ?>
<?php
	//Pokemon laden van de gebruiker die hij opzak heeft
	$poke = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");

	//Pokemons die hij opzak heeft weergeven  
	for($teller=0;$pokemon=$poke->fetch_assoc();++$teller) {
		$pokemon = pokemonei($pokemon, $txt);
		$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);
		echo '<tr>';
			//Als pokemon geen baby is
			if ($pokemon['baby'] != 'Ja' && $pokemon['level'] < 100)	echo '<td align="center"><input type="radio" name="pokemonid" value="'.$pokemon['id'].'" /></td>
				<td align="center"><input type="text" name="num_'.$pokemon['id'].'" placeholder="Ex: 1" style="width: 50px;" /></td>';
			else	echo '<td align="center"><input type="radio" name="pokemonid" value="'.$pokemon['id'].'" disabled /></td></td>
				<td align="center"><input type="text" name="num_'.$pokemon['id'].'" placeholder="Ex: 1" style="width: 50px;" disabled /></td>';
			echo '<td align="center" width="32"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" width="32" height="32" /></td>
				<td width="168">'.$pokemon['naam'].'</td>
				<td align="center">'.$pokemon['level'].'</td>
		</tr>';
	}

	if ($continue) {
?>
<script type="text/javascript">
	var num = parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html().replace('x', '').replace('<b>', '').replace('</b>', '');
	if ((num - 1) > 0)	parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html('<b>'+(num - 1)+'x</b>');
	else {
		parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').empty().parent().remove();
		parent.$.colorbox.close();
	}
</script>
<?php
	}
?>
		</tbody>
		<tfoot><tr>
			<td colspan="5" align="right">
				<input type="hidden" name="item" value="<?=$_GET['name'];?>" />
				<input type="submit" name="spcitem" value="Ok" class="button blue" />
			</td>
		</tr></tfoot>
	</table></div>
</form>
</body>
</html>
<?php } ?>